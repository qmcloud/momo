<?php

namespace PhpZip\IO;

use PhpZip\Constants\DosCodePage;
use PhpZip\Constants\ZipCompressionMethod;
use PhpZip\Constants\ZipConstants;
use PhpZip\Constants\ZipEncryptionMethod;
use PhpZip\Constants\ZipPlatform;
use PhpZip\Constants\ZipVersion;
use PhpZip\Exception\ZipException;
use PhpZip\Exception\ZipUnsupportMethodException;
use PhpZip\IO\Filter\Cipher\Pkware\PKEncryptionStreamFilter;
use PhpZip\IO\Filter\Cipher\WinZipAes\WinZipAesEncryptionStreamFilter;
use PhpZip\Model\Data\ZipSourceFileData;
use PhpZip\Model\Extra\Fields\ApkAlignmentExtraField;
use PhpZip\Model\Extra\Fields\WinZipAesExtraField;
use PhpZip\Model\Extra\Fields\Zip64ExtraField;
use PhpZip\Model\ZipContainer;
use PhpZip\Model\ZipEntry;
use PhpZip\Util\PackUtil;
use PhpZip\Util\StringUtil;

/**
 * Class ZipWriter.
 */
class ZipWriter
{
    /** @var int Chunk read size */
    const CHUNK_SIZE = 8192;

    /** @var ZipContainer */
    protected $zipContainer;

    /**
     * ZipWriter constructor.
     *
     * @param ZipContainer $container
     */
    public function __construct(ZipContainer $container)
    {
        // we clone the container so that the changes made to
        // it do not affect the data in the ZipFile class
        $this->zipContainer = clone $container;
    }

    /**
     * @param resource $outStream
     *
     * @throws ZipException
     */
    public function write($outStream)
    {
        if (!\is_resource($outStream)) {
            throw new \InvalidArgumentException('$outStream must be resource');
        }
        $this->beforeWrite();
        $this->writeLocalBlock($outStream);
        $cdOffset = ftell($outStream);
        $this->writeCentralDirectoryBlock($outStream);
        $cdSize = ftell($outStream) - $cdOffset;
        $this->writeEndOfCentralDirectoryBlock($outStream, $cdOffset, $cdSize);
    }

    protected function beforeWrite()
    {
    }

    /**
     * @param resource $outStream
     *
     * @throws ZipException
     */
    protected function writeLocalBlock($outStream)
    {
        $zipEntries = $this->zipContainer->getEntries();

        foreach ($zipEntries as $zipEntry) {
            $this->writeLocalHeader($outStream, $zipEntry);
            $this->writeData($outStream, $zipEntry);

            if ($zipEntry->isDataDescriptorEnabled()) {
                $this->writeDataDescriptor($outStream, $zipEntry);
            }
        }
    }

    /**
     * @param resource $outStream
     * @param ZipEntry $entry
     *
     * @throws ZipException
     */
    protected function writeLocalHeader($outStream, ZipEntry $entry)
    {
        // todo in 4.0 version move zipalign functional to ApkWriter class
        if ($this->zipContainer->isZipAlign()) {
            $this->zipAlign($outStream, $entry);
        }

        $relativeOffset = ftell($outStream);
        $entry->setLocalHeaderOffset($relativeOffset);

        if ($entry->isEncrypted() && $entry->getEncryptionMethod() === ZipEncryptionMethod::PKWARE) {
            $entry->enableDataDescriptor(true);
        }

        $dd = $entry->isDataDescriptorRequired() ||
            $entry->isDataDescriptorEnabled();

        $compressedSize = $entry->getCompressedSize();
        $uncompressedSize = $entry->getUncompressedSize();

        $entry->getLocalExtraFields()->remove(Zip64ExtraField::HEADER_ID);

        if ($compressedSize > ZipConstants::ZIP64_MAGIC || $uncompressedSize > ZipConstants::ZIP64_MAGIC) {
            $entry->getLocalExtraFields()->add(
                new Zip64ExtraField($uncompressedSize, $compressedSize)
            );

            $compressedSize = ZipConstants::ZIP64_MAGIC;
            $uncompressedSize = ZipConstants::ZIP64_MAGIC;
        }

        $compressionMethod = $entry->getCompressionMethod();
        $crc = $entry->getCrc();

        if ($entry->isEncrypted() && ZipEncryptionMethod::isWinZipAesMethod($entry->getEncryptionMethod())) {
            /** @var WinZipAesExtraField|null $winZipAesExtra */
            $winZipAesExtra = $entry->getLocalExtraField(WinZipAesExtraField::HEADER_ID);

            if ($winZipAesExtra === null) {
                $winZipAesExtra = WinZipAesExtraField::create($entry);
            }

            if ($winZipAesExtra->isV2()) {
                $crc = 0;
            }
            $compressionMethod = ZipCompressionMethod::WINZIP_AES;
        }

        $extra = $this->getExtraFieldsContents($entry, true);
        $name = $entry->getName();
        $dosCharset = $entry->getCharset();

        if ($dosCharset !== null && !$entry->isUtf8Flag()) {
            $name = DosCodePage::fromUTF8($name, $dosCharset);
        }

        $nameLength = \strlen($name);
        $extraLength = \strlen($extra);

        $size = $nameLength + $extraLength;

        if ($size > 0xffff) {
            throw new ZipException(
                sprintf(
                    '%s (the total size of %s bytes for the name, extra fields and comment exceeds the maximum size of %d bytes)',
                    $entry->getName(),
                    $size,
                    0xffff
                )
            );
        }

        $extractedBy = ($entry->getExtractedOS() << 8) | $entry->getExtractVersion();

        fwrite(
            $outStream,
            pack(
                'VvvvVVVVvv',
                // local file header signature     4 bytes  (0x04034b50)
                ZipConstants::LOCAL_FILE_HEADER,
                // version needed to extract       2 bytes
                $extractedBy,
                // general purpose bit flag        2 bytes
                $entry->getGeneralPurposeBitFlags(),
                // compression method              2 bytes
                $compressionMethod,
                // last mod file time              2 bytes
                // last mod file date              2 bytes
                $entry->getDosTime(),
                // crc-32                          4 bytes
                $dd ? 0 : $crc,
                // compressed size                 4 bytes
                $dd ? 0 : $compressedSize,
                // uncompressed size               4 bytes
                $dd ? 0 : $uncompressedSize,
                // file name length                2 bytes
                $nameLength,
                // extra field length              2 bytes
                $extraLength
            )
        );

        if ($nameLength > 0) {
            fwrite($outStream, $name);
        }

        if ($extraLength > 0) {
            fwrite($outStream, $extra);
        }
    }

    /**
     * @param resource $outStream
     * @param ZipEntry $entry
     *
     * @throws ZipException
     */
    private function zipAlign($outStream, ZipEntry $entry)
    {
        if (!$entry->isDirectory() && $entry->getCompressionMethod() === ZipCompressionMethod::STORED) {
            $entry->removeExtraField(ApkAlignmentExtraField::HEADER_ID);

            $extra = $this->getExtraFieldsContents($entry, true);
            $extraLength = \strlen($extra);
            $name = $entry->getName();

            $dosCharset = $entry->getCharset();

            if ($dosCharset !== null && !$entry->isUtf8Flag()) {
                $name = DosCodePage::fromUTF8($name, $dosCharset);
            }
            $nameLength = \strlen($name);

            $multiple = ApkAlignmentExtraField::ALIGNMENT_BYTES;

            if (StringUtil::endsWith($name, '.so')) {
                $multiple = ApkAlignmentExtraField::COMMON_PAGE_ALIGNMENT_BYTES;
            }

            $offset = ftell($outStream);

            $dataMinStartOffset =
                $offset +
                ZipConstants::LFH_FILENAME_POS +
                $extraLength +
                $nameLength;

            $padding =
                ($multiple - ($dataMinStartOffset % $multiple))
                % $multiple;

            if ($padding > 0) {
                $dataMinStartOffset += ApkAlignmentExtraField::MIN_SIZE;
                $padding =
                    ($multiple - ($dataMinStartOffset % $multiple))
                    % $multiple;

                $entry->getLocalExtraFields()->add(
                    new ApkAlignmentExtraField($multiple, $padding)
                );
            }
        }
    }

    /**
     * Merges the local file data fields of the given ZipExtraFields.
     *
     * @param ZipEntry $entry
     * @param bool     $local
     *
     * @throws ZipException
     *
     * @return string
     */
    protected function getExtraFieldsContents(ZipEntry $entry, $local)
    {
        $local = (bool) $local;
        $collection = $local ?
            $entry->getLocalExtraFields() :
            $entry->getCdExtraFields();
        $extraData = '';

        foreach ($collection as $extraField) {
            if ($local) {
                $data = $extraField->packLocalFileData();
            } else {
                $data = $extraField->packCentralDirData();
            }
            $extraData .= pack(
                'vv',
                $extraField->getHeaderId(),
                \strlen($data)
            );
            $extraData .= $data;
        }

        $size = \strlen($extraData);

        if ($size > 0xffff) {
            throw new ZipException(
                sprintf(
                    'Size extra out of range: %d. Extra data: %s',
                    $size,
                    $extraData
                )
            );
        }

        return $extraData;
    }

    /**
     * @param resource $outStream
     * @param ZipEntry $entry
     *
     * @throws ZipException
     */
    protected function writeData($outStream, ZipEntry $entry)
    {
        $zipData = $entry->getData();

        if ($zipData === null) {
            if ($entry->isDirectory()) {
                return;
            }

            throw new ZipException(sprintf('No zip data for entry "%s"', $entry->getName()));
        }

        // data write variants:
        // --------------------
        // * data of source zip file -> copy compressed data
        // * store - simple write
        // * store and encryption - apply encryption filter and simple write
        // * deflate or bzip2 - apply compression filter and simple write
        // * (deflate or bzip2) and encryption - create temp stream and apply
        //     compression filter to it, then apply encryption filter to root
        //     stream and write temp stream data.
        //     (PHP cannot apply the filter for encryption after the compression
        //     filter, so a temporary stream is created for the compressed data)

        if ($zipData instanceof ZipSourceFileData && !$zipData->hasRecompressData($entry)) {
            // data of source zip file -> copy compressed data
            $zipData->copyCompressedDataToStream($outStream);

            return;
        }

        $entryStream = $zipData->getDataAsStream();

        if (stream_get_meta_data($entryStream)['seekable']) {
            rewind($entryStream);
        }

        $uncompressedSize = $entry->getUncompressedSize();

        $posBeforeWrite = ftell($outStream);
        $compressionMethod = $entry->getCompressionMethod();

        if ($entry->isEncrypted()) {
            if ($compressionMethod === ZipCompressionMethod::STORED) {
                $contextFilter = $this->appendEncryptionFilter($outStream, $entry, $uncompressedSize);
                $checksum = $this->writeAndCountChecksum($entryStream, $outStream, $uncompressedSize);
            } else {
                $compressStream = fopen('php://temp', 'w+b');
                $contextFilter = $this->appendCompressionFilter($compressStream, $entry);
                $checksum = $this->writeAndCountChecksum($entryStream, $compressStream, $uncompressedSize);

                if ($contextFilter !== null) {
                    stream_filter_remove($contextFilter);
                    $contextFilter = null;
                }

                rewind($compressStream);

                $compressedSize = fstat($compressStream)['size'];
                $contextFilter = $this->appendEncryptionFilter($outStream, $entry, $compressedSize);

                stream_copy_to_stream($compressStream, $outStream);
            }
        } else {
            $contextFilter = $this->appendCompressionFilter($outStream, $entry);
            $checksum = $this->writeAndCountChecksum($entryStream, $outStream, $uncompressedSize);
        }

        if ($contextFilter !== null) {
            stream_filter_remove($contextFilter);
            $contextFilter = null;
        }

        // my hack {@see https://bugs.php.net/bug.php?id=49874}
        fseek($outStream, 0, \SEEK_END);
        $compressedSize = ftell($outStream) - $posBeforeWrite;

        $entry->setCompressedSize($compressedSize);
        $entry->setCrc($checksum);

        if (!$entry->isDataDescriptorEnabled()) {
            if ($uncompressedSize > ZipConstants::ZIP64_MAGIC || $compressedSize > ZipConstants::ZIP64_MAGIC) {
                /** @var Zip64ExtraField|null $zip64ExtraLocal */
                $zip64ExtraLocal = $entry->getLocalExtraField(Zip64ExtraField::HEADER_ID);

                // if there is a zip64 extra record, then update it;
                // if not, write data to data descriptor
                if ($zip64ExtraLocal !== null) {
                    $zip64ExtraLocal->setCompressedSize($compressedSize);
                    $zip64ExtraLocal->setUncompressedSize($uncompressedSize);

                    $posExtra = $entry->getLocalHeaderOffset() + ZipConstants::LFH_FILENAME_POS + \strlen($entry->getName());
                    fseek($outStream, $posExtra);
                    fwrite($outStream, $this->getExtraFieldsContents($entry, true));
                } else {
                    $posGPBF = $entry->getLocalHeaderOffset() + 6;
                    $entry->enableDataDescriptor(true);
                    fseek($outStream, $posGPBF);
                    fwrite(
                        $outStream,
                        pack(
                            'v',
                            // general purpose bit flag        2 bytes
                            $entry->getGeneralPurposeBitFlags()
                        )
                    );
                }

                $compressedSize = ZipConstants::ZIP64_MAGIC;
                $uncompressedSize = ZipConstants::ZIP64_MAGIC;
            }

            $posChecksum = $entry->getLocalHeaderOffset() + 14;

            /** @var WinZipAesExtraField|null $winZipAesExtra */
            $winZipAesExtra = $entry->getLocalExtraField(WinZipAesExtraField::HEADER_ID);

            if ($winZipAesExtra !== null && $winZipAesExtra->isV2()) {
                $checksum = 0;
            }

            fseek($outStream, $posChecksum);
            fwrite(
                $outStream,
                pack(
                    'VVV',
                    // crc-32                          4 bytes
                    $checksum,
                    // compressed size                 4 bytes
                    $compressedSize,
                    // uncompressed size               4 bytes
                    $uncompressedSize
                )
            );
            fseek($outStream, 0, \SEEK_END);
        }
    }

    /**
     * @param resource $inStream
     * @param resource $outStream
     * @param int      $size
     *
     * @return int
     */
    private function writeAndCountChecksum($inStream, $outStream, $size)
    {
        $contextHash = hash_init('crc32b');
        $offset = 0;

        while ($offset < $size) {
            $read = min(self::CHUNK_SIZE, $size - $offset);
            $buffer = fread($inStream, $read);
            fwrite($outStream, $buffer);
            hash_update($contextHash, $buffer);
            $offset += $read;
        }

        return (int) hexdec(hash_final($contextHash));
    }

    /**
     * @param resource $outStream
     * @param ZipEntry $entry
     *
     * @throws ZipUnsupportMethodException
     *
     * @return resource|null
     */
    protected function appendCompressionFilter($outStream, ZipEntry $entry)
    {
        $contextCompress = null;
        switch ($entry->getCompressionMethod()) {
            case ZipCompressionMethod::DEFLATED:
                if (!($contextCompress = stream_filter_append(
                    $outStream,
                    'zlib.deflate',
                    \STREAM_FILTER_WRITE,
                    ['level' => $entry->getCompressionLevel()]
                ))) {
                    throw new \RuntimeException('Could not append filter "zlib.deflate" to out stream');
                }
                break;

            case ZipCompressionMethod::BZIP2:
                if (!($contextCompress = stream_filter_append(
                    $outStream,
                    'bzip2.compress',
                    \STREAM_FILTER_WRITE,
                    ['blocks' => $entry->getCompressionLevel(), 'work' => 0]
                ))) {
                    throw new \RuntimeException('Could not append filter "bzip2.compress" to out stream');
                }
                break;

            case ZipCompressionMethod::STORED:
                // file without compression, do nothing
                break;

            default:
                throw new ZipUnsupportMethodException(
                    sprintf(
                        '%s (compression method %d (%s) is not supported)',
                        $entry->getName(),
                        $entry->getCompressionMethod(),
                        ZipCompressionMethod::getCompressionMethodName($entry->getCompressionMethod())
                    )
                );
        }

        return $contextCompress;
    }

    /**
     * @param resource $outStream
     * @param ZipEntry $entry
     * @param int      $size
     *
     * @return resource|null
     */
    protected function appendEncryptionFilter($outStream, ZipEntry $entry, $size)
    {
        $encContextFilter = null;

        if ($entry->isEncrypted()) {
            if ($entry->getEncryptionMethod() === ZipEncryptionMethod::PKWARE) {
                PKEncryptionStreamFilter::register();
                $cipherFilterName = PKEncryptionStreamFilter::FILTER_NAME;
            } else {
                WinZipAesEncryptionStreamFilter::register();
                $cipherFilterName = WinZipAesEncryptionStreamFilter::FILTER_NAME;
            }
            $encContextFilter = stream_filter_append(
                $outStream,
                $cipherFilterName,
                \STREAM_FILTER_WRITE,
                [
                    'entry' => $entry,
                    'size' => $size,
                ]
            );

            if (!$encContextFilter) {
                throw new \RuntimeException('Not apply filter ' . $cipherFilterName);
            }
        }

        return $encContextFilter;
    }

    /**
     * @param resource $outStream
     * @param ZipEntry $entry
     */
    protected function writeDataDescriptor($outStream, ZipEntry $entry)
    {
        $crc = $entry->getCrc();

        /** @var WinZipAesExtraField|null $winZipAesExtra */
        $winZipAesExtra = $entry->getLocalExtraField(WinZipAesExtraField::HEADER_ID);

        if ($winZipAesExtra !== null && $winZipAesExtra->isV2()) {
            $crc = 0;
        }

        fwrite(
            $outStream,
            pack(
                'VV',
                // data descriptor signature       4 bytes  (0x08074b50)
                ZipConstants::DATA_DESCRIPTOR,
                // crc-32                          4 bytes
                $crc
            )
        );

        if (
            $entry->isZip64ExtensionsRequired() ||
            $entry->getLocalExtraFields()->has(Zip64ExtraField::HEADER_ID)
        ) {
            $dd =
                // compressed size                 8 bytes
                PackUtil::packLongLE($entry->getCompressedSize()) .
                // uncompressed size               8 bytes
                PackUtil::packLongLE($entry->getUncompressedSize());
        } else {
            $dd = pack(
                'VV',
                // compressed size                 4 bytes
                $entry->getCompressedSize(),
                // uncompressed size               4 bytes
                $entry->getUncompressedSize()
            );
        }

        fwrite($outStream, $dd);
    }

    /**
     * @param resource $outStream
     *
     * @throws ZipException
     */
    protected function writeCentralDirectoryBlock($outStream)
    {
        foreach ($this->zipContainer->getEntries() as $outputEntry) {
            $this->writeCentralDirectoryHeader($outStream, $outputEntry);
        }
    }

    /**
     * Writes a Central File Header record.
     *
     * @param resource $outStream
     * @param ZipEntry $entry
     *
     * @throws ZipException
     */
    protected function writeCentralDirectoryHeader($outStream, ZipEntry $entry)
    {
        $compressedSize = $entry->getCompressedSize();
        $uncompressedSize = $entry->getUncompressedSize();
        $localHeaderOffset = $entry->getLocalHeaderOffset();

        $entry->getCdExtraFields()->remove(Zip64ExtraField::HEADER_ID);

        if (
            $localHeaderOffset > ZipConstants::ZIP64_MAGIC ||
            $compressedSize > ZipConstants::ZIP64_MAGIC ||
            $uncompressedSize > ZipConstants::ZIP64_MAGIC
        ) {
            $zip64ExtraField = new Zip64ExtraField();

            if ($uncompressedSize >= ZipConstants::ZIP64_MAGIC) {
                $zip64ExtraField->setUncompressedSize($uncompressedSize);
                $uncompressedSize = ZipConstants::ZIP64_MAGIC;
            }

            if ($compressedSize >= ZipConstants::ZIP64_MAGIC) {
                $zip64ExtraField->setCompressedSize($compressedSize);
                $compressedSize = ZipConstants::ZIP64_MAGIC;
            }

            if ($localHeaderOffset >= ZipConstants::ZIP64_MAGIC) {
                $zip64ExtraField->setLocalHeaderOffset($localHeaderOffset);
                $localHeaderOffset = ZipConstants::ZIP64_MAGIC;
            }

            $entry->getCdExtraFields()->add($zip64ExtraField);
        }

        $extra = $this->getExtraFieldsContents($entry, false);
        $extraLength = \strlen($extra);

        $name = $entry->getName();
        $comment = $entry->getComment();

        $dosCharset = $entry->getCharset();

        if ($dosCharset !== null && !$entry->isUtf8Flag()) {
            $name = DosCodePage::fromUTF8($name, $dosCharset);

            if ($comment) {
                $comment = DosCodePage::fromUTF8($comment, $dosCharset);
            }
        }

        $commentLength = \strlen($comment);

        $compressionMethod = $entry->getCompressionMethod();
        $crc = $entry->getCrc();

        /** @var WinZipAesExtraField|null $winZipAesExtra */
        $winZipAesExtra = $entry->getLocalExtraField(WinZipAesExtraField::HEADER_ID);

        if ($winZipAesExtra !== null) {
            if ($winZipAesExtra->isV2()) {
                $crc = 0;
            }
            $compressionMethod = ZipCompressionMethod::WINZIP_AES;
        }

        fwrite(
            $outStream,
            pack(
                'VvvvvVVVVvvvvvVV',
                // central file header signature   4 bytes  (0x02014b50)
                ZipConstants::CENTRAL_FILE_HEADER,
                // version made by                 2 bytes
                ($entry->getCreatedOS() << 8) | $entry->getSoftwareVersion(),
                // version needed to extract       2 bytes
                ($entry->getExtractedOS() << 8) | $entry->getExtractVersion(),
                // general purpose bit flag        2 bytes
                $entry->getGeneralPurposeBitFlags(),
                // compression method              2 bytes
                $compressionMethod,
                // last mod file datetime          4 bytes
                $entry->getDosTime(),
                // crc-32                          4 bytes
                $crc,
                // compressed size                 4 bytes
                $compressedSize,
                // uncompressed size               4 bytes
                $uncompressedSize,
                // file name length                2 bytes
                \strlen($name),
                // extra field length              2 bytes
                $extraLength,
                // file comment length             2 bytes
                $commentLength,
                // disk number start               2 bytes
                0,
                // internal file attributes        2 bytes
                $entry->getInternalAttributes(),
                // external file attributes        4 bytes
                $entry->getExternalAttributes(),
                // relative offset of local header 4 bytes
                $localHeaderOffset
            )
        );

        // file name (variable size)
        fwrite($outStream, $name);

        if ($extraLength > 0) {
            // extra field (variable size)
            fwrite($outStream, $extra);
        }

        if ($commentLength > 0) {
            // file comment (variable size)
            fwrite($outStream, $comment);
        }
    }

    /**
     * @param resource $outStream
     * @param int      $centralDirectoryOffset
     * @param int      $centralDirectorySize
     */
    protected function writeEndOfCentralDirectoryBlock(
        $outStream,
        $centralDirectoryOffset,
        $centralDirectorySize
    ) {
        $cdEntriesCount = \count($this->zipContainer);

        $cdEntriesZip64 = $cdEntriesCount > 0xffff;
        $cdSizeZip64 = $centralDirectorySize > ZipConstants::ZIP64_MAGIC;
        $cdOffsetZip64 = $centralDirectoryOffset > ZipConstants::ZIP64_MAGIC;

        $zip64Required = $cdEntriesZip64
            || $cdSizeZip64
            || $cdOffsetZip64;

        if ($zip64Required) {
            $zip64EndOfCentralDirectoryOffset = ftell($outStream);

            // find max software version, version needed to extract and most common platform
            list($softwareVersion, $versionNeededToExtract) = array_reduce(
                $this->zipContainer->getEntries(),
                static function (array $carry, ZipEntry $entry) {
                    $carry[0] = max($carry[0], $entry->getSoftwareVersion() & 0xFF);
                    $carry[1] = max($carry[1], $entry->getExtractVersion() & 0xFF);

                    return $carry;
                },
                [ZipVersion::v10_DEFAULT_MIN, ZipVersion::v45_ZIP64_EXT]
            );

            $createdOS = $extractedOS = ZipPlatform::OS_DOS;
            $versionMadeBy = ($createdOS << 8) | max($softwareVersion, ZipVersion::v45_ZIP64_EXT);
            $versionExtractedBy = ($extractedOS << 8) | max($versionNeededToExtract, ZipVersion::v45_ZIP64_EXT);

            // write zip64 end of central directory signature
            fwrite(
                $outStream,
                pack(
                    'V',
                    // signature                       4 bytes  (0x06064b50)
                    ZipConstants::ZIP64_END_CD
                )
            );
            // size of zip64 end of central
            // directory record                8 bytes
            fwrite($outStream, PackUtil::packLongLE(ZipConstants::ZIP64_END_OF_CD_LEN - 12));
            fwrite(
                $outStream,
                pack(
                    'vvVV',
                    // version made by                 2 bytes
                    $versionMadeBy & 0xFFFF,
                    // version needed to extract       2 bytes
                    $versionExtractedBy & 0xFFFF,
                    // number of this disk             4 bytes
                    0,
                    // number of the disk with the
                    // start of the central directory  4 bytes
                    0
                )
            );

            fwrite(
                $outStream,
                // total number of entries in the
                // central directory on this disk  8 bytes
                PackUtil::packLongLE($cdEntriesCount) .
                // total number of entries in the
                // central directory               8 bytes
                PackUtil::packLongLE($cdEntriesCount) .
                // size of the central directory   8 bytes
                PackUtil::packLongLE($centralDirectorySize) .
                // offset of start of central
                // directory with respect to
                // the starting disk number        8 bytes
                PackUtil::packLongLE($centralDirectoryOffset)
            );

            // write zip64 end of central directory locator
            fwrite(
                $outStream,
                pack(
                    'VV',
                    // zip64 end of central dir locator
                    // signature                       4 bytes  (0x07064b50)
                    ZipConstants::ZIP64_END_CD_LOC,
                    // number of the disk with the
                    // start of the zip64 end of
                    // central directory               4 bytes
                    0
                ) .
                // relative offset of the zip64
                // end of central directory record 8 bytes
                PackUtil::packLongLE($zip64EndOfCentralDirectoryOffset) .
                // total number of disks           4 bytes
                pack('V', 1)
            );
        }

        $comment = $this->zipContainer->getArchiveComment();
        $commentLength = $comment !== null ? \strlen($comment) : 0;

        fwrite(
            $outStream,
            pack(
                'VvvvvVVv',
                // end of central dir signature    4 bytes  (0x06054b50)
                ZipConstants::END_CD,
                // number of this disk             2 bytes
                0,
                // number of the disk with the
                // start of the central directory  2 bytes
                0,
                // total number of entries in the
                // central directory on this disk  2 bytes
                $cdEntriesZip64 ? 0xffff : $cdEntriesCount,
                // total number of entries in
                // the central directory           2 bytes
                $cdEntriesZip64 ? 0xffff : $cdEntriesCount,
                // size of the central directory   4 bytes
                $cdSizeZip64 ? ZipConstants::ZIP64_MAGIC : $centralDirectorySize,
                // offset of start of central
                // directory with respect to
                // the starting disk number        4 bytes
                $cdOffsetZip64 ? ZipConstants::ZIP64_MAGIC : $centralDirectoryOffset,
                // .ZIP file comment length        2 bytes
                $commentLength
            )
        );

        if ($comment !== null && $commentLength > 0) {
            // .ZIP file comment       (variable size)
            fwrite($outStream, $comment);
        }
    }
}
