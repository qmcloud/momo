<?php

namespace PhpZip\IO;

use PhpZip\Constants\DosCodePage;
use PhpZip\Constants\GeneralPurposeBitFlag;
use PhpZip\Constants\ZipCompressionMethod;
use PhpZip\Constants\ZipConstants;
use PhpZip\Constants\ZipEncryptionMethod;
use PhpZip\Constants\ZipOptions;
use PhpZip\Exception\Crc32Exception;
use PhpZip\Exception\InvalidArgumentException;
use PhpZip\Exception\ZipException;
use PhpZip\IO\Filter\Cipher\Pkware\PKDecryptionStreamFilter;
use PhpZip\IO\Filter\Cipher\WinZipAes\WinZipAesDecryptionStreamFilter;
use PhpZip\Model\Data\ZipSourceFileData;
use PhpZip\Model\EndOfCentralDirectory;
use PhpZip\Model\Extra\ExtraFieldsCollection;
use PhpZip\Model\Extra\Fields\UnicodePathExtraField;
use PhpZip\Model\Extra\Fields\UnrecognizedExtraField;
use PhpZip\Model\Extra\Fields\WinZipAesExtraField;
use PhpZip\Model\Extra\Fields\Zip64ExtraField;
use PhpZip\Model\Extra\ZipExtraDriver;
use PhpZip\Model\Extra\ZipExtraField;
use PhpZip\Model\ImmutableZipContainer;
use PhpZip\Model\ZipEntry;
use PhpZip\Util\PackUtil;

/**
 * Zip reader.
 *
 * @author Ne-Lexa alexey@nelexa.ru
 * @license MIT
 */
class ZipReader
{
    /** @var int file size */
    protected $size;

    /** @var resource */
    protected $inStream;

    /** @var array */
    protected $options;

    /**
     * @param resource $inStream
     * @param array    $options
     */
    public function __construct($inStream, array $options = [])
    {
        if (!\is_resource($inStream)) {
            throw new InvalidArgumentException('Stream must be a resource');
        }
        $type = get_resource_type($inStream);

        if ($type !== 'stream') {
            throw new InvalidArgumentException("Invalid resource type {$type}.");
        }
        $meta = stream_get_meta_data($inStream);

        $wrapperType = isset($meta['wrapper_type']) ? $meta['wrapper_type'] : 'Unknown';
        $supportStreamWrapperTypes = ['plainfile', 'PHP', 'user-space'];

        if (!\in_array($wrapperType, $supportStreamWrapperTypes, true)) {
            throw new InvalidArgumentException(
                'The stream wrapper type "' . $wrapperType . '" is not supported. Support: ' . implode(
                    ', ',
                    $supportStreamWrapperTypes
                )
            );
        }

        if (
            $wrapperType === 'plainfile' &&
            (
                $meta['stream_type'] === 'dir' ||
                (isset($meta['uri']) && is_dir($meta['uri']))
            )
        ) {
            throw new InvalidArgumentException('Directory stream not supported');
        }

        $seekable = $meta['seekable'];

        if (!$seekable) {
            throw new InvalidArgumentException('Resource does not support seekable.');
        }
        $this->size = fstat($inStream)['size'];
        $this->inStream = $inStream;

        /** @noinspection AdditionOperationOnArraysInspection */
        $options += $this->getDefaultOptions();
        $this->options = $options;
    }

    /**
     * @return array
     */
    protected function getDefaultOptions()
    {
        return [
            ZipOptions::CHARSET => null,
        ];
    }

    /**
     * @throws ZipException
     *
     * @return ImmutableZipContainer
     */
    public function read()
    {
        if ($this->size < ZipConstants::END_CD_MIN_LEN) {
            throw new ZipException('Corrupt zip file');
        }

        $endOfCentralDirectory = $this->readEndOfCentralDirectory();
        $entries = $this->readCentralDirectory($endOfCentralDirectory);

        return new ImmutableZipContainer($entries, $endOfCentralDirectory->getComment());
    }

    /**
     * @return array
     */
    public function getStreamMetaData()
    {
        return stream_get_meta_data($this->inStream);
    }

    /**
     * Read End of central directory record.
     *
     * end of central dir signature    4 bytes  (0x06054b50)
     * number of this disk             2 bytes
     * number of the disk with the
     * start of the central directory  2 bytes
     * total number of entries in the
     * central directory on this disk  2 bytes
     * total number of entries in
     * the central directory           2 bytes
     * size of the central directory   4 bytes
     * offset of start of central
     * directory with respect to
     * the starting disk number        4 bytes
     * .ZIP file comment length        2 bytes
     * .ZIP file comment       (variable size)
     *
     * @throws ZipException
     *
     * @return EndOfCentralDirectory
     */
    protected function readEndOfCentralDirectory()
    {
        if (!$this->findEndOfCentralDirectory()) {
            throw new ZipException('Invalid zip file. The end of the central directory could not be found.');
        }

        $positionECD = ftell($this->inStream) - 4;
        $sizeECD = $this->size - ftell($this->inStream);
        $buffer = fread($this->inStream, $sizeECD);

        $unpack = unpack(
            'vdiskNo/vcdDiskNo/vcdEntriesDisk/' .
            'vcdEntries/VcdSize/VcdPos/vcommentLength',
            substr($buffer, 0, 18)
        );

        if (
            $unpack['diskNo'] !== 0 ||
            $unpack['cdDiskNo'] !== 0 ||
            $unpack['cdEntriesDisk'] !== $unpack['cdEntries']
        ) {
            throw new ZipException(
                'ZIP file spanning/splitting is not supported!'
            );
        }
        // .ZIP file comment       (variable sizeECD)
        $comment = null;

        if ($unpack['commentLength'] > 0) {
            $comment = substr($buffer, 18, $unpack['commentLength']);
        }

        // Check for ZIP64 End Of Central Directory Locator exists.
        $zip64ECDLocatorPosition = $positionECD - ZipConstants::ZIP64_END_CD_LOC_LEN;
        fseek($this->inStream, $zip64ECDLocatorPosition);
        // zip64 end of central dir locator
        // signature                       4 bytes  (0x07064b50)
        if ($zip64ECDLocatorPosition > 0 && unpack(
            'V',
            fread($this->inStream, 4)
        )[1] === ZipConstants::ZIP64_END_CD_LOC) {
            if (!$this->isZip64Support()) {
                throw new ZipException('ZIP64 not supported this archive.');
            }

            $positionECD = $this->findZip64ECDPosition();
            $endCentralDirectory = $this->readZip64EndOfCentralDirectory($positionECD);
            $endCentralDirectory->setComment($comment);
        } else {
            $endCentralDirectory = new EndOfCentralDirectory(
                $unpack['cdEntries'],
                $unpack['cdPos'],
                $unpack['cdSize'],
                false,
                $comment
            );
        }

        return $endCentralDirectory;
    }

    /**
     * @return bool
     */
    protected function findEndOfCentralDirectory()
    {
        $max = $this->size - ZipConstants::END_CD_MIN_LEN;
        $min = $max >= 0xffff ? $max - 0xffff : 0;
        // Search for End of central directory record.
        for ($position = $max; $position >= $min; $position--) {
            fseek($this->inStream, $position);
            // end of central dir signature    4 bytes  (0x06054b50)
            if (unpack('V', fread($this->inStream, 4))[1] !== ZipConstants::END_CD) {
                continue;
            }

            return true;
        }

        return false;
    }

    /**
     * Read Zip64 end of central directory locator and returns
     * Zip64 end of central directory position.
     *
     * number of the disk with the
     * start of the zip64 end of
     * central directory               4 bytes
     * relative offset of the zip64
     * end of central directory record 8 bytes
     * total number of disks           4 bytes
     *
     * @throws ZipException
     *
     * @return int Zip64 End Of Central Directory position
     */
    protected function findZip64ECDPosition()
    {
        $diskNo = unpack('V', fread($this->inStream, 4))[1];
        $zip64ECDPos = PackUtil::unpackLongLE(fread($this->inStream, 8));
        $totalDisks = unpack('V', fread($this->inStream, 4))[1];

        if ($diskNo !== 0 || $totalDisks > 1) {
            throw new ZipException('ZIP file spanning/splitting is not supported!');
        }

        return $zip64ECDPos;
    }

    /**
     * Read zip64 end of central directory locator and zip64 end
     * of central directory record.
     *
     * zip64 end of central dir
     * signature                       4 bytes  (0x06064b50)
     * size of zip64 end of central
     * directory record                8 bytes
     * version made by                 2 bytes
     * version needed to extract       2 bytes
     * number of this disk             4 bytes
     * number of the disk with the
     * start of the central directory  4 bytes
     * total number of entries in the
     * central directory on this disk  8 bytes
     * total number of entries in the
     * central directory               8 bytes
     * size of the central directory   8 bytes
     * offset of start of central
     * directory with respect to
     * the starting disk number        8 bytes
     * zip64 extensible data sector    (variable size)
     *
     * @param int $zip64ECDPosition
     *
     * @throws ZipException
     *
     * @return EndOfCentralDirectory
     */
    protected function readZip64EndOfCentralDirectory($zip64ECDPosition)
    {
        fseek($this->inStream, $zip64ECDPosition);

        $buffer = fread($this->inStream, ZipConstants::ZIP64_END_OF_CD_LEN);

        if (unpack('V', $buffer)[1] !== ZipConstants::ZIP64_END_CD) {
            throw new ZipException('Expected ZIP64 End Of Central Directory Record!');
        }

        $data = unpack(
//            'Psize/vversionMadeBy/vextractVersion/' .
            'VdiskNo/VcdDiskNo',
            substr($buffer, 16, 8)
        );

        $cdEntriesDisk = PackUtil::unpackLongLE(substr($buffer, 24, 8));
        $entryCount = PackUtil::unpackLongLE(substr($buffer, 32, 8));
        $cdSize = PackUtil::unpackLongLE(substr($buffer, 40, 8));
        $cdPos = PackUtil::unpackLongLE(substr($buffer, 48, 8));

//        $platform = ZipPlatform::fromValue(($data['versionMadeBy'] & 0xFF00) >> 8);
//        $softwareVersion = $data['versionMadeBy'] & 0x00FF;

        if ($data['diskNo'] !== 0 || $data['cdDiskNo'] !== 0 || $entryCount !== $cdEntriesDisk) {
            throw new ZipException('ZIP file spanning/splitting is not supported!');
        }

        if ($entryCount < 0 || $entryCount > 0x7fffffff) {
            throw new ZipException('Total Number Of Entries In The Central Directory out of range!');
        }

        // skip zip64 extensible data sector (variable sizeEndCD)

        return new EndOfCentralDirectory(
            $entryCount,
            $cdPos,
            $cdSize,
            true
        );
    }

    /**
     * Reads the central directory from the given seekable byte channel
     * and populates the internal tables with ZipEntry instances.
     *
     * The ZipEntry's will know all data that can be obtained from the
     * central directory alone, but not the data that requires the local
     * file header or additional data to be read.
     *
     * @param EndOfCentralDirectory $endCD
     *
     * @throws ZipException
     *
     * @return ZipEntry[]
     */
    protected function readCentralDirectory(EndOfCentralDirectory $endCD)
    {
        $entries = [];

        $cdOffset = $endCD->getCdOffset();
        fseek($this->inStream, $cdOffset);

        if (!($cdStream = fopen('php://temp', 'w+b'))) {
            // @codeCoverageIgnoreStart
            throw new ZipException('A temporary resource cannot be opened for writing.');
            // @codeCoverageIgnoreEnd
        }
        stream_copy_to_stream($this->inStream, $cdStream, $endCD->getCdSize());
        rewind($cdStream);
        for ($numEntries = $endCD->getEntryCount(); $numEntries > 0; $numEntries--) {
            $zipEntry = $this->readZipEntry($cdStream);

            $entryName = $zipEntry->getName();

            /** @var UnicodePathExtraField|null $unicodePathExtraField */
            $unicodePathExtraField = $zipEntry->getExtraField(UnicodePathExtraField::HEADER_ID);

            if ($unicodePathExtraField !== null && $unicodePathExtraField->getCrc32() === crc32($entryName)) {
                $unicodePath = $unicodePathExtraField->getUnicodeValue();

                if ($unicodePath !== null) {
                    $unicodePath = str_replace('\\', '/', $unicodePath);

                    if (
                        $unicodePath !== '' &&
                        substr_count($entryName, '/') === substr_count($unicodePath, '/')
                    ) {
                        $entryName = $unicodePath;
                    }
                }
            }

            $entries[$entryName] = $zipEntry;
        }

        return $entries;
    }

    /**
     * Read central directory entry.
     *
     * central file header signature   4 bytes  (0x02014b50)
     * version made by                 2 bytes
     * version needed to extract       2 bytes
     * general purpose bit flag        2 bytes
     * compression method              2 bytes
     * last mod file time              2 bytes
     * last mod file date              2 bytes
     * crc-32                          4 bytes
     * compressed size                 4 bytes
     * uncompressed size               4 bytes
     * file name length                2 bytes
     * extra field length              2 bytes
     * file comment length             2 bytes
     * disk number start               2 bytes
     * internal file attributes        2 bytes
     * external file attributes        4 bytes
     * relative offset of local header 4 bytes
     *
     * file name (variable size)
     * extra field (variable size)
     * file comment (variable size)
     *
     * @param resource $stream
     *
     * @throws ZipException
     *
     * @return ZipEntry
     */
    protected function readZipEntry($stream)
    {
        if (unpack('V', fread($stream, 4))[1] !== ZipConstants::CENTRAL_FILE_HEADER) {
            throw new ZipException('Corrupt zip file. Cannot read zip entry.');
        }

        $unpack = unpack(
            'vversionMadeBy/vversionNeededToExtract/' .
            'vgeneralPurposeBitFlag/vcompressionMethod/' .
            'VlastModFile/Vcrc/VcompressedSize/' .
            'VuncompressedSize/vfileNameLength/vextraFieldLength/' .
            'vfileCommentLength/vdiskNumberStart/vinternalFileAttributes/' .
            'VexternalFileAttributes/VoffsetLocalHeader',
            fread($stream, 42)
        );

        if ($unpack['diskNumberStart'] !== 0) {
            throw new ZipException('ZIP file spanning/splitting is not supported!');
        }

        $generalPurposeBitFlags = $unpack['generalPurposeBitFlag'];
        $isUtf8 = ($generalPurposeBitFlags & GeneralPurposeBitFlag::UTF8) !== 0;

        $name = fread($stream, $unpack['fileNameLength']);

        $createdOS = ($unpack['versionMadeBy'] & 0xFF00) >> 8;
        $softwareVersion = $unpack['versionMadeBy'] & 0x00FF;

        $extractedOS = ($unpack['versionNeededToExtract'] & 0xFF00) >> 8;
        $extractVersion = $unpack['versionNeededToExtract'] & 0x00FF;

        $dosTime = $unpack['lastModFile'];

        $comment = null;

        if ($unpack['fileCommentLength'] > 0) {
            $comment = fread($stream, $unpack['fileCommentLength']);
        }

        // decode code page names
        $fallbackCharset = null;

        if (!$isUtf8 && isset($this->options[ZipOptions::CHARSET])) {
            $charset = $this->options[ZipOptions::CHARSET];

            $fallbackCharset = $charset;
            $name = DosCodePage::toUTF8($name, $charset);

            if ($comment !== null) {
                $comment = DosCodePage::toUTF8($comment, $charset);
            }
        }

        $zipEntry = ZipEntry::create(
            $name,
            $createdOS,
            $extractedOS,
            $softwareVersion,
            $extractVersion,
            $unpack['compressionMethod'],
            $generalPurposeBitFlags,
            $dosTime,
            $unpack['crc'],
            $unpack['compressedSize'],
            $unpack['uncompressedSize'],
            $unpack['internalFileAttributes'],
            $unpack['externalFileAttributes'],
            $unpack['offsetLocalHeader'],
            $comment,
            $fallbackCharset
        );

        if ($unpack['extraFieldLength'] > 0) {
            $this->parseExtraFields(
                fread($stream, $unpack['extraFieldLength']),
                $zipEntry,
                false
            );

            /** @var Zip64ExtraField|null $extraZip64 */
            $extraZip64 = $zipEntry->getCdExtraField(Zip64ExtraField::HEADER_ID);

            if ($extraZip64 !== null) {
                $this->handleZip64Extra($extraZip64, $zipEntry);
            }
        }

        $this->loadLocalExtraFields($zipEntry);
        $this->handleExtraEncryptionFields($zipEntry);
        $this->handleExtraFields($zipEntry);

        return $zipEntry;
    }

    /**
     * @param string   $buffer
     * @param ZipEntry $zipEntry
     * @param bool     $local
     *
     * @return ExtraFieldsCollection
     */
    protected function parseExtraFields($buffer, ZipEntry $zipEntry, $local = false)
    {
        $collection = $local ?
            $zipEntry->getLocalExtraFields() :
            $zipEntry->getCdExtraFields();

        if (!empty($buffer)) {
            $pos = 0;
            $endPos = \strlen($buffer);

            while ($endPos - $pos >= 4) {
                /** @var int[] $data */
                $data = unpack('vheaderId/vdataSize', substr($buffer, $pos, 4));
                $pos += 4;

                if ($endPos - $pos - $data['dataSize'] < 0) {
                    break;
                }
                $bufferData = substr($buffer, $pos, $data['dataSize']);
                $headerId = $data['headerId'];

                /** @var string|ZipExtraField|null $className */
                $className = ZipExtraDriver::getClassNameOrNull($headerId);

                try {
                    if ($className !== null) {
                        try {
                            $extraField = $local ?
                                \call_user_func([$className, 'unpackLocalFileData'], $bufferData, $zipEntry) :
                                \call_user_func([$className, 'unpackCentralDirData'], $bufferData, $zipEntry);
                        } catch (\Throwable $e) {
                            // skip errors while parsing invalid data
                            continue;
                        }
                    } else {
                        $extraField = new UnrecognizedExtraField($headerId, $bufferData);
                    }
                    $collection->add($extraField);
                } finally {
                    $pos += $data['dataSize'];
                }
            }
        }

        return $collection;
    }

    /**
     * @param Zip64ExtraField $extraZip64
     * @param ZipEntry        $zipEntry
     */
    protected function handleZip64Extra(Zip64ExtraField $extraZip64, ZipEntry $zipEntry)
    {
        $uncompressedSize = $extraZip64->getUncompressedSize();
        $compressedSize = $extraZip64->getCompressedSize();
        $localHeaderOffset = $extraZip64->getLocalHeaderOffset();

        if ($uncompressedSize !== null) {
            $zipEntry->setUncompressedSize($uncompressedSize);
        }

        if ($compressedSize !== null) {
            $zipEntry->setCompressedSize($compressedSize);
        }

        if ($localHeaderOffset !== null) {
            $zipEntry->setLocalHeaderOffset($localHeaderOffset);
        }
    }

    /**
     * Read Local File Header.
     *
     * local file header signature     4 bytes  (0x04034b50)
     * version needed to extract       2 bytes
     * general purpose bit flag        2 bytes
     * compression method              2 bytes
     * last mod file time              2 bytes
     * last mod file date              2 bytes
     * crc-32                          4 bytes
     * compressed size                 4 bytes
     * uncompressed size               4 bytes
     * file name length                2 bytes
     * extra field length              2 bytes
     * file name (variable size)
     * extra field (variable size)
     *
     * @param ZipEntry $entry
     *
     * @throws ZipException
     */
    protected function loadLocalExtraFields(ZipEntry $entry)
    {
        $offsetLocalHeader = $entry->getLocalHeaderOffset();

        fseek($this->inStream, $offsetLocalHeader);

        if (unpack('V', fread($this->inStream, 4))[1] !== ZipConstants::LOCAL_FILE_HEADER) {
            throw new ZipException(sprintf('%s (expected Local File Header)', $entry->getName()));
        }

        fseek($this->inStream, $offsetLocalHeader + ZipConstants::LFH_FILENAME_LENGTH_POS);
        $unpack = unpack('vfileNameLength/vextraFieldLength', fread($this->inStream, 4));
        $offsetData = ftell($this->inStream)
            + $unpack['fileNameLength']
            + $unpack['extraFieldLength'];

        fseek($this->inStream, $unpack['fileNameLength'], \SEEK_CUR);

        if ($unpack['extraFieldLength'] > 0) {
            $this->parseExtraFields(
                fread($this->inStream, $unpack['extraFieldLength']),
                $entry,
                true
            );
        }

        $zipData = new ZipSourceFileData($this, $entry, $offsetData);
        $entry->setData($zipData);
    }

    /**
     * @param ZipEntry $zipEntry
     *
     * @throws ZipException
     */
    private function handleExtraEncryptionFields(ZipEntry $zipEntry)
    {
        if ($zipEntry->isEncrypted()) {
            if ($zipEntry->getCompressionMethod() === ZipCompressionMethod::WINZIP_AES) {
                /** @var WinZipAesExtraField|null $extraField */
                $extraField = $zipEntry->getExtraField(WinZipAesExtraField::HEADER_ID);

                if ($extraField === null) {
                    throw new ZipException(
                        sprintf(
                            'Extra field 0x%04x (WinZip-AES Encryption) expected for compression method %d',
                            WinZipAesExtraField::HEADER_ID,
                            $zipEntry->getCompressionMethod()
                        )
                    );
                }
                $zipEntry->setCompressionMethod($extraField->getCompressionMethod());
                $zipEntry->setEncryptionMethod($extraField->getEncryptionMethod());
            } else {
                $zipEntry->setEncryptionMethod(ZipEncryptionMethod::PKWARE);
            }
        }
    }

    /**
     * Handle extra data in zip records.
     *
     * This is a special method in which you can process ExtraField
     * and make changes to ZipEntry.
     *
     * @param ZipEntry $zipEntry
     */
    protected function handleExtraFields(ZipEntry $zipEntry)
    {
    }

    /**
     * @param ZipSourceFileData $zipFileData
     *
     * @throws ZipException
     * @throws Crc32Exception
     *
     * @return resource
     */
    public function getEntryStream(ZipSourceFileData $zipFileData)
    {
        $outStream = fopen('php://temp', 'w+b');
        $this->copyUncompressedDataToStream($zipFileData, $outStream);
        rewind($outStream);

        return $outStream;
    }

    /**
     * @param ZipSourceFileData $zipFileData
     * @param resource          $outStream
     *
     * @throws Crc32Exception
     * @throws ZipException
     */
    public function copyUncompressedDataToStream(ZipSourceFileData $zipFileData, $outStream)
    {
        if (!\is_resource($outStream)) {
            throw new InvalidArgumentException('outStream is not resource');
        }

        $entry = $zipFileData->getSourceEntry();

//        if ($entry->isDirectory()) {
//            throw new InvalidArgumentException('Streams not supported for directories');
//        }

        if ($entry->isStrongEncryption()) {
            throw new ZipException('Not support encryption zip.');
        }

        $compressionMethod = $entry->getCompressionMethod();

        fseek($this->inStream, $zipFileData->getOffset());

        $filters = [];

        $skipCheckCrc = false;
        $isEncrypted = $entry->isEncrypted();

        if ($isEncrypted) {
            if ($entry->getPassword() === null) {
                throw new ZipException('Can not password from entry ' . $entry->getName());
            }

            if (ZipEncryptionMethod::isWinZipAesMethod($entry->getEncryptionMethod())) {
                /** @var WinZipAesExtraField|null $winZipAesExtra */
                $winZipAesExtra = $entry->getExtraField(WinZipAesExtraField::HEADER_ID);

                if ($winZipAesExtra === null) {
                    throw new ZipException(
                        sprintf('WinZip AES must contain the extra field %s', WinZipAesExtraField::HEADER_ID)
                    );
                }
                $compressionMethod = $winZipAesExtra->getCompressionMethod();

                WinZipAesDecryptionStreamFilter::register();
                $cipherFilterName = WinZipAesDecryptionStreamFilter::FILTER_NAME;

                if ($winZipAesExtra->isV2()) {
                    $skipCheckCrc = true;
                }
            } else {
                PKDecryptionStreamFilter::register();
                $cipherFilterName = PKDecryptionStreamFilter::FILTER_NAME;
            }
            $encContextFilter = stream_filter_append(
                $this->inStream,
                $cipherFilterName,
                \STREAM_FILTER_READ,
                [
                    'entry' => $entry,
                ]
            );

            if (!$encContextFilter) {
                throw new \RuntimeException('Not apply filter ' . $cipherFilterName);
            }
            $filters[] = $encContextFilter;
        }

        // hack, see https://groups.google.com/forum/#!topic/alt.comp.lang.php/37_JZeW63uc
        $pos = ftell($this->inStream);
        rewind($this->inStream);
        fseek($this->inStream, $pos);

        $contextDecompress = null;
        switch ($compressionMethod) {
            case ZipCompressionMethod::STORED:
                // file without compression, do nothing
                break;

            case ZipCompressionMethod::DEFLATED:
                if (!($contextDecompress = stream_filter_append(
                    $this->inStream,
                    'zlib.inflate',
                    \STREAM_FILTER_READ
                ))) {
                    throw new \RuntimeException('Could not append filter "zlib.inflate" to stream');
                }
                $filters[] = $contextDecompress;

                break;

            case ZipCompressionMethod::BZIP2:
                if (!($contextDecompress = stream_filter_append(
                    $this->inStream,
                    'bzip2.decompress',
                    \STREAM_FILTER_READ
                ))) {
                    throw new \RuntimeException('Could not append filter "bzip2.decompress" to stream');
                }
                $filters[] = $contextDecompress;

                break;

            default:
                throw new ZipException(
                    sprintf(
                        '%s (compression method %d (%s) is not supported)',
                        $entry->getName(),
                        $compressionMethod,
                        ZipCompressionMethod::getCompressionMethodName($compressionMethod)
                    )
                );
        }

        $limit = $zipFileData->getUncompressedSize();

        $offset = 0;
        $chunkSize = 8192;

        try {
            if ($skipCheckCrc) {
                while ($offset < $limit) {
                    $length = min($chunkSize, $limit - $offset);
                    $buffer = fread($this->inStream, $length);

                    if ($buffer === false) {
                        throw new ZipException(sprintf('Error reading the contents of entry "%s".', $entry->getName()));
                    }
                    fwrite($outStream, $buffer);
                    $offset += $length;
                }
            } else {
                $contextHash = hash_init('crc32b');

                while ($offset < $limit) {
                    $length = min($chunkSize, $limit - $offset);
                    $buffer = fread($this->inStream, $length);

                    if ($buffer === false) {
                        throw new ZipException(sprintf('Error reading the contents of entry "%s".', $entry->getName()));
                    }
                    fwrite($outStream, $buffer);
                    hash_update($contextHash, $buffer);
                    $offset += $length;
                }

                $expectedCrc = (int) hexdec(hash_final($contextHash));

                if ($expectedCrc !== $entry->getCrc()) {
                    throw new Crc32Exception($entry->getName(), $expectedCrc, $entry->getCrc());
                }
            }
        } finally {
            for ($i = \count($filters); $i > 0; $i--) {
                stream_filter_remove($filters[$i - 1]);
            }
        }
    }

    /**
     * @param ZipSourceFileData $zipData
     * @param resource          $outStream
     */
    public function copyCompressedDataToStream(ZipSourceFileData $zipData, $outStream)
    {
        if ($zipData->getCompressedSize() > 0) {
            fseek($this->inStream, $zipData->getOffset());
            stream_copy_to_stream($this->inStream, $outStream, $zipData->getCompressedSize());
        }
    }

    /**
     * @return bool
     */
    protected function isZip64Support()
    {
        return \PHP_INT_SIZE === 8; // true for 64bit system
    }

    public function close()
    {
        if (\is_resource($this->inStream)) {
            fclose($this->inStream);
        }
    }

    public function __destruct()
    {
        $this->close();
    }
}
