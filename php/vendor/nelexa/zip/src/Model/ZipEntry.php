<?php

/** @noinspection PhpUsageOfSilenceOperatorInspection */

namespace PhpZip\Model;

use PhpZip\Constants\DosAttrs;
use PhpZip\Constants\DosCodePage;
use PhpZip\Constants\GeneralPurposeBitFlag;
use PhpZip\Constants\UnixStat;
use PhpZip\Constants\ZipCompressionLevel;
use PhpZip\Constants\ZipCompressionMethod;
use PhpZip\Constants\ZipConstants;
use PhpZip\Constants\ZipEncryptionMethod;
use PhpZip\Constants\ZipPlatform;
use PhpZip\Constants\ZipVersion;
use PhpZip\Exception\InvalidArgumentException;
use PhpZip\Exception\RuntimeException;
use PhpZip\Exception\ZipUnsupportMethodException;
use PhpZip\Model\Extra\ExtraFieldsCollection;
use PhpZip\Model\Extra\Fields\AsiExtraField;
use PhpZip\Model\Extra\Fields\ExtendedTimestampExtraField;
use PhpZip\Model\Extra\Fields\NtfsExtraField;
use PhpZip\Model\Extra\Fields\OldUnixExtraField;
use PhpZip\Model\Extra\Fields\UnicodePathExtraField;
use PhpZip\Model\Extra\Fields\WinZipAesExtraField;
use PhpZip\Model\Extra\ZipExtraField;
use PhpZip\Util\DateTimeConverter;
use PhpZip\Util\StringUtil;

/**
 * ZIP file entry.
 *
 * @see     https://pkware.cachefly.net/webdocs/casestudies/APPNOTE.TXT .ZIP File Format Specification
 *
 * @author  Ne-Lexa alexey@nelexa.ru
 * @license MIT
 */
class ZipEntry
{
    /** @var int the unknown value for numeric properties */
    const UNKNOWN = -1;

    /**
     * @var int DOS platform
     *
     * @deprecated Use {@see ZipPlatform::OS_DOS}
     */
    const PLATFORM_FAT = ZipPlatform::OS_DOS;

    /**
     * @var int Unix platform
     *
     * @deprecated Use {@see ZipPlatform::OS_UNIX}
     */
    const PLATFORM_UNIX = ZipPlatform::OS_UNIX;

    /**
     * @var int MacOS platform
     *
     * @deprecated Use {@see ZipPlatform::OS_MAC_OSX}
     */
    const PLATFORM_OS_X = ZipPlatform::OS_MAC_OSX;

    /**
     * Pseudo compression method for WinZip AES encrypted entries.
     * Require php extension openssl or mcrypt.
     *
     * @deprecated Use {@see ZipCompressionMethod::WINZIP_AES}
     */
    const METHOD_WINZIP_AES = ZipCompressionMethod::WINZIP_AES;

    /** @var string Entry name (filename in archive) */
    private $name;

    /** @var bool Is directory */
    private $isDirectory;

    /** @var ZipData|null Zip entry contents */
    private $data;

    /** @var int Made by platform */
    private $createdOS = self::UNKNOWN;

    /** @var int Extracted by platform */
    private $extractedOS = self::UNKNOWN;

    /** @var int Software version */
    private $softwareVersion = self::UNKNOWN;

    /** @var int Version needed to extract */
    private $extractVersion = self::UNKNOWN;

    /** @var int Compression method */
    private $compressionMethod = self::UNKNOWN;

    /** @var int General purpose bit flags */
    private $generalPurposeBitFlags = 0;

    /** @var int Dos time */
    private $dosTime = self::UNKNOWN;

    /** @var int Crc32 */
    private $crc = self::UNKNOWN;

    /** @var int Compressed size */
    private $compressedSize = self::UNKNOWN;

    /** @var int Uncompressed size */
    private $uncompressedSize = self::UNKNOWN;

    /** @var int Internal attributes */
    private $internalAttributes = 0;

    /** @var int External attributes */
    private $externalAttributes = 0;

    /** @var int relative Offset Of Local File Header */
    private $localHeaderOffset = 0;

    /**
     * Collections of Extra Fields in Central Directory.
     * Keys from Header ID [int] and value Extra Field [ExtraField].
     *
     * @var ExtraFieldsCollection
     */
    protected $cdExtraFields;

    /**
     * Collections of Extra Fields int local header.
     * Keys from Header ID [int] and value Extra Field [ExtraField].
     *
     * @var ExtraFieldsCollection
     */
    protected $localExtraFields;

    /** @var string|null comment field */
    private $comment;

    /** @var string|null entry password for read or write encryption data */
    private $password;

    /** @var int encryption method */
    private $encryptionMethod = ZipEncryptionMethod::NONE;

    /** @var int */
    private $compressionLevel = ZipCompressionLevel::NORMAL;

    /** @var string|null */
    private $charset;

    /**
     * ZipEntry constructor.
     *
     * @param string      $name    Entry name
     * @param string|null $charset DOS charset
     */
    public function __construct($name, $charset = null)
    {
        $this->setName($name, $charset);

        $this->cdExtraFields = new ExtraFieldsCollection();
        $this->localExtraFields = new ExtraFieldsCollection();
    }

    /**
     * This method only internal use.
     *
     * @param string      $name
     * @param int         $createdOS
     * @param int         $extractedOS
     * @param int         $softwareVersion
     * @param int         $extractVersion
     * @param int         $compressionMethod
     * @param int         $gpbf
     * @param int         $dosTime
     * @param int         $crc
     * @param int         $compressedSize
     * @param int         $uncompressedSize
     * @param int         $internalAttributes
     * @param int         $externalAttributes
     * @param int         $offsetLocalHeader
     * @param string|null $comment
     * @param string|null $charset
     *
     * @return ZipEntry
     *
     * @internal
     *
     * @noinspection PhpTooManyParametersInspection
     */
    public static function create(
        $name,
        $createdOS,
        $extractedOS,
        $softwareVersion,
        $extractVersion,
        $compressionMethod,
        $gpbf,
        $dosTime,
        $crc,
        $compressedSize,
        $uncompressedSize,
        $internalAttributes,
        $externalAttributes,
        $offsetLocalHeader,
        $comment,
        $charset
    ) {
        $entry = new self($name);
        $entry->createdOS = (int) $createdOS;
        $entry->extractedOS = (int) $extractedOS;
        $entry->softwareVersion = (int) $softwareVersion;
        $entry->extractVersion = (int) $extractVersion;
        $entry->compressionMethod = (int) $compressionMethod;
        $entry->generalPurposeBitFlags = (int) $gpbf;
        $entry->dosTime = (int) $dosTime;
        $entry->crc = (int) $crc;
        $entry->compressedSize = (int) $compressedSize;
        $entry->uncompressedSize = (int) $uncompressedSize;
        $entry->internalAttributes = (int) $internalAttributes;
        $entry->externalAttributes = (int) $externalAttributes;
        $entry->localHeaderOffset = (int) $offsetLocalHeader;
        $entry->setComment($comment);
        $entry->setCharset($charset);
        $entry->updateCompressionLevel();

        return $entry;
    }

    /**
     * Set entry name.
     *
     * @param string      $name    New entry name
     * @param string|null $charset
     *
     * @return ZipEntry
     */
    private function setName($name, $charset = null)
    {
        if ($name === null) {
            throw new InvalidArgumentException('zip entry name is null');
        }

        $name = ltrim((string) $name, '\\/');

        if ($name === '') {
            throw new InvalidArgumentException('Empty zip entry name');
        }

        $name = (string) $name;
        $length = \strlen($name);

        if ($length > 0xffff) {
            throw new InvalidArgumentException('Illegal zip entry name parameter');
        }

        $this->setCharset($charset);

        if ($this->charset === null && !StringUtil::isASCII($name)) {
            $this->enableUtf8Name(true);
        }
        $this->name = $name;
        $this->isDirectory = ($length = \strlen($name)) >= 1 && $name[$length - 1] === '/';
        $this->externalAttributes = $this->isDirectory ? DosAttrs::DOS_DIRECTORY : DosAttrs::DOS_ARCHIVE;

        if ($this->extractVersion !== self::UNKNOWN) {
            $this->extractVersion = max(
                $this->extractVersion,
                $this->isDirectory ?
                    ZipVersion::v20_DEFLATED_FOLDER_ZIPCRYPTO :
                    ZipVersion::v10_DEFAULT_MIN
            );
        }

        return $this;
    }

    /**
     * @param string|null $charset
     *
     * @return ZipEntry
     *
     * @see DosCodePage::getCodePages()
     */
    public function setCharset($charset = null)
    {
        if ($charset !== null && $charset === '') {
            throw new InvalidArgumentException('Empty charset');
        }
        $this->charset = $charset;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getCharset()
    {
        return $this->charset;
    }

    /**
     * @param string $newName New entry name
     *
     * @return ZipEntry new {@see ZipEntry} object with new name
     *
     * @internal
     */
    public function rename($newName)
    {
        $newEntry = clone $this;
        $newEntry->setName($newName);

        $newEntry->removeExtraField(UnicodePathExtraField::HEADER_ID);

        return $newEntry;
    }

    /**
     * Returns the ZIP entry name.
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return ZipData|null
     *
     * @internal
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @param ZipData|null $data
     *
     * @internal
     */
    public function setData($data)
    {
        $this->data = $data;
    }

    /**
     * @return int Get platform
     *
     * @deprecated Use {@see ZipEntry::getCreatedOS()}
     */
    public function getPlatform()
    {
        @trigger_error(__METHOD__ . ' is deprecated. Use ' . __CLASS__ . '::getCreatedOS()', \E_USER_DEPRECATED);

        return $this->getCreatedOS();
    }

    /**
     * @param int $platform
     *
     * @return ZipEntry
     *
     * @deprecated Use {@see ZipEntry::setCreatedOS()}
     */
    public function setPlatform($platform)
    {
        @trigger_error(__METHOD__ . ' is deprecated. Use ' . __CLASS__ . '::setCreatedOS()', \E_USER_DEPRECATED);

        return $this->setCreatedOS($platform);
    }

    /**
     * @return int platform
     */
    public function getCreatedOS()
    {
        return $this->createdOS;
    }

    /**
     * Set platform.
     *
     * @param int $platform
     *
     * @return ZipEntry
     */
    public function setCreatedOS($platform)
    {
        $platform = (int) $platform;

        if ($platform < 0x00 || $platform > 0xff) {
            throw new InvalidArgumentException('Platform out of range');
        }
        $this->createdOS = $platform;

        return $this;
    }

    /**
     * @return int
     */
    public function getExtractedOS()
    {
        return $this->extractedOS;
    }

    /**
     * Set extracted OS.
     *
     * @param int $platform
     *
     * @return ZipEntry
     */
    public function setExtractedOS($platform)
    {
        $platform = (int) $platform;

        if ($platform < 0x00 || $platform > 0xff) {
            throw new InvalidArgumentException('Platform out of range');
        }
        $this->extractedOS = $platform;

        return $this;
    }

    /**
     * @return int
     */
    public function getSoftwareVersion()
    {
        if ($this->softwareVersion === self::UNKNOWN) {
            return $this->getExtractVersion();
        }

        return $this->softwareVersion;
    }

    /**
     * @param int $softwareVersion
     *
     * @return ZipEntry
     */
    public function setSoftwareVersion($softwareVersion)
    {
        $this->softwareVersion = (int) $softwareVersion;

        return $this;
    }

    /**
     * Version needed to extract.
     *
     * @return int
     *
     * @deprecated Use {@see ZipEntry::getExtractVersion()}
     */
    public function getVersionNeededToExtract()
    {
        @trigger_error(__METHOD__ . ' is deprecated. Use ' . __CLASS__ . '::getExtractVersion()', \E_USER_DEPRECATED);

        return $this->getExtractVersion();
    }

    /**
     * Version needed to extract.
     *
     * @return int
     */
    public function getExtractVersion()
    {
        if ($this->extractVersion === self::UNKNOWN) {
            if (ZipEncryptionMethod::isWinZipAesMethod($this->encryptionMethod)) {
                return ZipVersion::v51_ENCR_AES_RC2_CORRECT;
            }

            if ($this->compressionMethod === ZipCompressionMethod::BZIP2) {
                return ZipVersion::v46_BZIP2;
            }

            if ($this->isZip64ExtensionsRequired()) {
                return ZipVersion::v45_ZIP64_EXT;
            }

            if (
                $this->compressionMethod === ZipCompressionMethod::DEFLATED ||
                $this->isDirectory ||
                $this->encryptionMethod === ZipEncryptionMethod::PKWARE
            ) {
                return ZipVersion::v20_DEFLATED_FOLDER_ZIPCRYPTO;
            }

            return ZipVersion::v10_DEFAULT_MIN;
        }

        return $this->extractVersion;
    }

    /**
     * Set version needed to extract.
     *
     * @param int $version
     *
     * @return ZipEntry
     *
     * @deprecated Use {@see ZipEntry::setExtractVersion()}
     */
    public function setVersionNeededToExtract($version)
    {
        @trigger_error(__METHOD__ . ' is deprecated. Use ' . __CLASS__ . '::setExtractVersion()', \E_USER_DEPRECATED);

        return $this->setExtractVersion($version);
    }

    /**
     * Set version needed to extract.
     *
     * @param int $version
     *
     * @return ZipEntry
     */
    public function setExtractVersion($version)
    {
        $this->extractVersion = max(ZipVersion::v10_DEFAULT_MIN, (int) $version);

        return $this;
    }

    /**
     * Returns the compressed size of this entry.
     *
     * @return int
     */
    public function getCompressedSize()
    {
        return $this->compressedSize;
    }

    /**
     * Sets the compressed size of this entry.
     *
     * @param int $compressedSize the Compressed Size
     *
     * @return ZipEntry
     *
     * @internal
     */
    public function setCompressedSize($compressedSize)
    {
        $compressedSize = (int) $compressedSize;

        if ($compressedSize < self::UNKNOWN) {
            throw new InvalidArgumentException('Compressed size < ' . self::UNKNOWN);
        }
        $this->compressedSize = $compressedSize;

        return $this;
    }

    /**
     * Returns the uncompressed size of this entry.
     *
     * @return int
     *
     * @deprecated Use {@see ZipEntry::getUncompressedSize()}
     */
    public function getSize()
    {
        @trigger_error(__METHOD__ . ' is deprecated. Use ' . __CLASS__ . '::getUncompressedSize()', \E_USER_DEPRECATED);

        return $this->getUncompressedSize();
    }

    /**
     * Sets the uncompressed size of this entry.
     *
     * @param int $size the (Uncompressed) Size
     *
     * @return ZipEntry
     *
     * @deprecated Use {@see ZipEntry::setUncompressedSize()}
     *
     * @internal
     */
    public function setSize($size)
    {
        @trigger_error(__METHOD__ . ' is deprecated. Use ' . __CLASS__ . '::setUncompressedSize()', \E_USER_DEPRECATED);

        return $this->setUncompressedSize($size);
    }

    /**
     * Returns the uncompressed size of this entry.
     *
     * @return int
     */
    public function getUncompressedSize()
    {
        return $this->uncompressedSize;
    }

    /**
     * Sets the uncompressed size of this entry.
     *
     * @param int $uncompressedSize the (Uncompressed) Size
     *
     * @return ZipEntry
     *
     * @internal
     */
    public function setUncompressedSize($uncompressedSize)
    {
        $uncompressedSize = (int) $uncompressedSize;

        if ($uncompressedSize < self::UNKNOWN) {
            throw new InvalidArgumentException('Uncompressed size < ' . self::UNKNOWN);
        }
        $this->uncompressedSize = $uncompressedSize;

        return $this;
    }

    /**
     * Return relative Offset Of Local File Header.
     *
     * @return int
     */
    public function getLocalHeaderOffset()
    {
        return $this->localHeaderOffset;
    }

    /**
     * @param int $localHeaderOffset
     *
     * @return ZipEntry
     *
     * @internal
     */
    public function setLocalHeaderOffset($localHeaderOffset)
    {
        $localHeaderOffset = (int) $localHeaderOffset;

        if ($localHeaderOffset < 0) {
            throw new InvalidArgumentException('Negative $localHeaderOffset');
        }
        $this->localHeaderOffset = $localHeaderOffset;

        return $this;
    }

    /**
     * Return relative Offset Of Local File Header.
     *
     * @return int
     *
     * @deprecated Use {@see ZipEntry::getLocalHeaderOffset()}
     */
    public function getOffset()
    {
        @trigger_error(
            __METHOD__ . ' is deprecated. Use ' . __CLASS__ . '::getLocalHeaderOffset()',
            \E_USER_DEPRECATED
        );

        return $this->getLocalHeaderOffset();
    }

    /**
     * @param int $offset
     *
     * @return ZipEntry
     *
     * @deprecated Use {@see ZipEntry::setLocalHeaderOffset()}
     *
     * @internal
     */
    public function setOffset($offset)
    {
        @trigger_error(
            __METHOD__ . ' is deprecated. Use ' . __CLASS__ . '::setLocalHeaderOffset()',
            \E_USER_DEPRECATED
        );

        return $this->setLocalHeaderOffset($offset);
    }

    /**
     * Returns the General Purpose Bit Flags.
     *
     * @return int
     */
    public function getGeneralPurposeBitFlags()
    {
        return $this->generalPurposeBitFlags;
    }

    /**
     * Sets the General Purpose Bit Flags.
     *
     * @param int $gpbf general purpose bit flags
     *
     * @return ZipEntry
     *
     * @internal
     */
    public function setGeneralPurposeBitFlags($gpbf)
    {
        $gpbf = (int) $gpbf;

        if ($gpbf < 0x0000 || $gpbf > 0xffff) {
            throw new InvalidArgumentException('general purpose bit flags out of range');
        }
        $this->generalPurposeBitFlags = $gpbf;
        $this->updateCompressionLevel();

        return $this;
    }

    private function updateCompressionLevel()
    {
        if ($this->compressionMethod === ZipCompressionMethod::DEFLATED) {
            $bit1 = $this->isSetGeneralBitFlag(GeneralPurposeBitFlag::COMPRESSION_FLAG1);
            $bit2 = $this->isSetGeneralBitFlag(GeneralPurposeBitFlag::COMPRESSION_FLAG2);

            if ($bit1 && !$bit2) {
                $this->compressionLevel = ZipCompressionLevel::MAXIMUM;
            } elseif (!$bit1 && $bit2) {
                $this->compressionLevel = ZipCompressionLevel::FAST;
            } elseif ($bit1 && $bit2) {
                $this->compressionLevel = ZipCompressionLevel::SUPER_FAST;
            } else {
                $this->compressionLevel = ZipCompressionLevel::NORMAL;
            }
        }
    }

    /**
     * @param int  $mask
     * @param bool $enable
     *
     * @return ZipEntry
     */
    private function setGeneralBitFlag($mask, $enable)
    {
        if ($enable) {
            $this->generalPurposeBitFlags |= $mask;
        } else {
            $this->generalPurposeBitFlags &= ~$mask;
        }

        return $this;
    }

    /**
     * @param int $mask
     *
     * @return bool
     */
    private function isSetGeneralBitFlag($mask)
    {
        return ($this->generalPurposeBitFlags & $mask) === $mask;
    }

    /**
     * @return bool
     */
    public function isDataDescriptorEnabled()
    {
        return $this->isSetGeneralBitFlag(GeneralPurposeBitFlag::DATA_DESCRIPTOR);
    }

    /**
     * Enabling or disabling the use of the Data Descriptor block.
     *
     * @param bool $enabled
     */
    public function enableDataDescriptor($enabled = true)
    {
        $this->setGeneralBitFlag(GeneralPurposeBitFlag::DATA_DESCRIPTOR, (bool) $enabled);
    }

    /**
     * @param bool $enabled
     */
    public function enableUtf8Name($enabled)
    {
        $this->setGeneralBitFlag(GeneralPurposeBitFlag::UTF8, (bool) $enabled);
    }

    /**
     * @return bool
     */
    public function isUtf8Flag()
    {
        return $this->isSetGeneralBitFlag(GeneralPurposeBitFlag::UTF8);
    }

    /**
     * Returns true if and only if this ZIP entry is encrypted.
     *
     * @return bool
     */
    public function isEncrypted()
    {
        return $this->isSetGeneralBitFlag(GeneralPurposeBitFlag::ENCRYPTION);
    }

    /**
     * @return bool
     */
    public function isStrongEncryption()
    {
        return $this->isSetGeneralBitFlag(GeneralPurposeBitFlag::STRONG_ENCRYPTION);
    }

    /**
     * Sets the encryption property to false and removes any other
     * encryption artifacts.
     *
     * @return ZipEntry
     */
    public function disableEncryption()
    {
        $this->setEncrypted(false);
        $this->removeExtraField(WinZipAesExtraField::HEADER_ID);
        $this->encryptionMethod = ZipEncryptionMethod::NONE;
        $this->password = null;
        $this->extractVersion = self::UNKNOWN;

        return $this;
    }

    /**
     * Sets the encryption flag for this ZIP entry.
     *
     * @param bool $encrypted
     *
     * @return ZipEntry
     */
    private function setEncrypted($encrypted)
    {
        $encrypted = (bool) $encrypted;
        $this->setGeneralBitFlag(GeneralPurposeBitFlag::ENCRYPTION, $encrypted);

        return $this;
    }

    /**
     * Returns the compression method for this entry.
     *
     * @return int
     *
     * @deprecated Use {@see ZipEntry::getCompressionMethod()}
     */
    public function getMethod()
    {
        @trigger_error(
            __METHOD__ . ' is deprecated. Use ' . __CLASS__ . '::getCompressionMethod()',
            \E_USER_DEPRECATED
        );

        return $this->getCompressionMethod();
    }

    /**
     * Returns the compression method for this entry.
     *
     * @return int
     */
    public function getCompressionMethod()
    {
        return $this->compressionMethod;
    }

    /**
     * Sets the compression method for this entry.
     *
     * @param int $method
     *
     * @throws ZipUnsupportMethodException
     *
     * @return ZipEntry
     *
     * @deprecated Use {@see ZipEntry::setCompressionMethod()}
     */
    public function setMethod($method)
    {
        @trigger_error(
            __METHOD__ . ' is deprecated. Use ' . __CLASS__ . '::setCompressionMethod()',
            \E_USER_DEPRECATED
        );

        return $this->setCompressionMethod($method);
    }

    /**
     * Sets the compression method for this entry.
     *
     * @param int $compressionMethod
     *
     * @throws ZipUnsupportMethodException
     *
     * @return ZipEntry
     *
     * @see ZipCompressionMethod::STORED
     * @see ZipCompressionMethod::DEFLATED
     * @see ZipCompressionMethod::BZIP2
     */
    public function setCompressionMethod($compressionMethod)
    {
        $compressionMethod = (int) $compressionMethod;

        if ($compressionMethod < 0x0000 || $compressionMethod > 0xffff) {
            throw new InvalidArgumentException('method out of range: ' . $compressionMethod);
        }

        ZipCompressionMethod::checkSupport($compressionMethod);

        $this->compressionMethod = $compressionMethod;
        $this->updateCompressionLevel();
        $this->extractVersion = self::UNKNOWN;

        return $this;
    }

    /**
     * Get Unix Timestamp.
     *
     * @return int
     */
    public function getTime()
    {
        if ($this->getDosTime() === self::UNKNOWN) {
            return self::UNKNOWN;
        }

        return DateTimeConverter::msDosToUnix($this->getDosTime());
    }

    /**
     * Get Dos Time.
     *
     * @return int
     */
    public function getDosTime()
    {
        return $this->dosTime;
    }

    /**
     * Set Dos Time.
     *
     * @param int $dosTime
     *
     * @return ZipEntry
     */
    public function setDosTime($dosTime)
    {
        $dosTime = (int) $dosTime;

        if (\PHP_INT_SIZE === 8) {
            if ($dosTime < 0x00000000 || $dosTime > 0xffffffff) {
                throw new InvalidArgumentException('DosTime out of range');
            }
        }

        $this->dosTime = $dosTime;

        return $this;
    }

    /**
     * Set time from unix timestamp.
     *
     * @param int $unixTimestamp
     *
     * @return ZipEntry
     */
    public function setTime($unixTimestamp)
    {
        if ($unixTimestamp !== self::UNKNOWN) {
            $this->setDosTime(DateTimeConverter::unixToMsDos($unixTimestamp));
        } else {
            $this->dosTime = 0;
        }

        return $this;
    }

    /**
     * Returns the external file attributes.
     *
     * @return int the external file attributes
     */
    public function getExternalAttributes()
    {
        return $this->externalAttributes;
    }

    /**
     * Sets the external file attributes.
     *
     * @param int $externalAttributes the external file attributes
     *
     * @return ZipEntry
     */
    public function setExternalAttributes($externalAttributes)
    {
        $this->externalAttributes = (int) $externalAttributes;

        if (\PHP_INT_SIZE === 8) {
            if ($externalAttributes < 0x00000000 || $externalAttributes > 0xffffffff) {
                throw new InvalidArgumentException('external attributes out of range: ' . $externalAttributes);
            }
        }

        $this->externalAttributes = $externalAttributes;

        return $this;
    }

    /**
     * Returns the internal file attributes.
     *
     * @return int the internal file attributes
     */
    public function getInternalAttributes()
    {
        return $this->internalAttributes;
    }

    /**
     * Sets the internal file attributes.
     *
     * @param int $internalAttributes the internal file attributes
     *
     * @return ZipEntry
     */
    public function setInternalAttributes($internalAttributes)
    {
        $internalAttributes = (int) $internalAttributes;

        if ($internalAttributes < 0x0000 || $internalAttributes > 0xffff) {
            throw new InvalidArgumentException('internal attributes out of range');
        }
        $this->internalAttributes = $internalAttributes;

        return $this;
    }

    /**
     * Returns true if and only if this ZIP entry represents a directory entry
     * (i.e. end with '/').
     *
     * @return bool
     */
    final public function isDirectory()
    {
        return $this->isDirectory;
    }

    /**
     * @return ExtraFieldsCollection
     */
    public function getCdExtraFields()
    {
        return $this->cdExtraFields;
    }

    /**
     * @param int $headerId
     *
     * @return ZipExtraField|null
     */
    public function getCdExtraField($headerId)
    {
        return $this->cdExtraFields->get((int) $headerId);
    }

    /**
     * @param ExtraFieldsCollection $cdExtraFields
     *
     * @return ZipEntry
     */
    public function setCdExtraFields(ExtraFieldsCollection $cdExtraFields)
    {
        $this->cdExtraFields = $cdExtraFields;

        return $this;
    }

    /**
     * @return ExtraFieldsCollection
     */
    public function getLocalExtraFields()
    {
        return $this->localExtraFields;
    }

    /**
     * @param int $headerId
     *
     * @return ZipExtraField|null
     */
    public function getLocalExtraField($headerId)
    {
        return $this->localExtraFields[(int) $headerId];
    }

    /**
     * @param ExtraFieldsCollection $localExtraFields
     *
     * @return ZipEntry
     */
    public function setLocalExtraFields(ExtraFieldsCollection $localExtraFields)
    {
        $this->localExtraFields = $localExtraFields;

        return $this;
    }

    /**
     * @param int $headerId
     *
     * @return ZipExtraField|null
     */
    public function getExtraField($headerId)
    {
        $headerId = (int) $headerId;
        $local = $this->getLocalExtraField($headerId);

        if ($local === null) {
            return $this->getCdExtraField($headerId);
        }

        return $local;
    }

    /**
     * @param int $headerId
     *
     * @return bool
     */
    public function hasExtraField($headerId)
    {
        $headerId = (int) $headerId;

        return
            isset($this->localExtraFields[$headerId]) ||
            isset($this->cdExtraFields[$headerId]);
    }

    /**
     * @param int $headerId
     */
    public function removeExtraField($headerId)
    {
        $headerId = (int) $headerId;

        $this->cdExtraFields->remove($headerId);
        $this->localExtraFields->remove($headerId);
    }

    /**
     * @param ZipExtraField $zipExtraField
     */
    public function addExtraField(ZipExtraField $zipExtraField)
    {
        $this->addLocalExtraField($zipExtraField);
        $this->addCdExtraField($zipExtraField);
    }

    /**
     * @param ZipExtraField $zipExtraField
     */
    public function addLocalExtraField(ZipExtraField $zipExtraField)
    {
        $this->localExtraFields->add($zipExtraField);
    }

    /**
     * @param ZipExtraField $zipExtraField
     */
    public function addCdExtraField(ZipExtraField $zipExtraField)
    {
        $this->cdExtraFields->add($zipExtraField);
    }

    /**
     * Returns comment entry.
     *
     * @return string
     */
    public function getComment()
    {
        return $this->comment !== null ? $this->comment : '';
    }

    /**
     * Set entry comment.
     *
     * @param string|null $comment
     *
     * @return ZipEntry
     */
    public function setComment($comment)
    {
        if ($comment !== null) {
            $commentLength = \strlen($comment);

            if ($commentLength > 0xffff) {
                throw new InvalidArgumentException('Comment too long');
            }

            if ($this->charset === null && !StringUtil::isASCII($comment)) {
                $this->enableUtf8Name(true);
            }
        }
        $this->comment = $comment;

        return $this;
    }

    /**
     * @return bool
     */
    public function isDataDescriptorRequired()
    {
        return ($this->getCrc() | $this->getCompressedSize() | $this->getUncompressedSize()) === self::UNKNOWN;
    }

    /**
     * Return crc32 content or 0 for WinZip AES v2.
     *
     * @return int
     */
    public function getCrc()
    {
        return $this->crc;
    }

    /**
     * Set crc32 content.
     *
     * @param int $crc
     *
     * @return ZipEntry
     *
     * @internal
     */
    public function setCrc($crc)
    {
        $this->crc = (int) $crc;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * Set password and encryption method from entry.
     *
     * @param string|null $password
     * @param int|null    $encryptionMethod
     *
     * @return ZipEntry
     */
    public function setPassword($password, $encryptionMethod = null)
    {
        if (!$this->isDirectory) {
            if ($password === null || $password === '') {
                $this->password = null;
                $this->disableEncryption();
            } else {
                $this->password = (string) $password;

                if ($encryptionMethod === null && $this->encryptionMethod === ZipEncryptionMethod::NONE) {
                    $encryptionMethod = ZipEncryptionMethod::WINZIP_AES_256;
                }

                if ($encryptionMethod !== null) {
                    $this->setEncryptionMethod($encryptionMethod);
                }
                $this->setEncrypted(true);
            }
        }

        return $this;
    }

    /**
     * @return int
     */
    public function getEncryptionMethod()
    {
        return $this->encryptionMethod;
    }

    /**
     * Set encryption method.
     *
     * @param int|null $encryptionMethod
     *
     * @return ZipEntry
     *
     * @see ZipEncryptionMethod::NONE
     * @see ZipEncryptionMethod::PKWARE
     * @see ZipEncryptionMethod::WINZIP_AES_256
     * @see ZipEncryptionMethod::WINZIP_AES_192
     * @see ZipEncryptionMethod::WINZIP_AES_128
     */
    public function setEncryptionMethod($encryptionMethod)
    {
        if ($encryptionMethod === null) {
            $encryptionMethod = ZipEncryptionMethod::NONE;
        }

        $encryptionMethod = (int) $encryptionMethod;
        ZipEncryptionMethod::checkSupport($encryptionMethod);
        $this->encryptionMethod = $encryptionMethod;

        $this->setEncrypted($this->encryptionMethod !== ZipEncryptionMethod::NONE);
        $this->extractVersion = self::UNKNOWN;

        return $this;
    }

    /**
     * @return int
     */
    public function getCompressionLevel()
    {
        return $this->compressionLevel;
    }

    /**
     * @param int $compressionLevel
     *
     * @return ZipEntry
     */
    public function setCompressionLevel($compressionLevel)
    {
        $compressionLevel = (int) $compressionLevel;

        if ($compressionLevel === self::UNKNOWN) {
            $compressionLevel = ZipCompressionLevel::NORMAL;
        }

        if (
            $compressionLevel < ZipCompressionLevel::LEVEL_MIN ||
            $compressionLevel > ZipCompressionLevel::LEVEL_MAX
        ) {
            throw new InvalidArgumentException(
                'Invalid compression level. Minimum level ' .
                ZipCompressionLevel::LEVEL_MIN . '. Maximum level ' . ZipCompressionLevel::LEVEL_MAX
            );
        }
        $this->compressionLevel = $compressionLevel;

        $this->updateGbpfCompLevel();

        return $this;
    }

    /**
     * Update general purpose bit flogs.
     */
    private function updateGbpfCompLevel()
    {
        if ($this->compressionMethod === ZipCompressionMethod::DEFLATED) {
            $bit1 = false;
            $bit2 = false;

            switch ($this->compressionLevel) {
                case ZipCompressionLevel::MAXIMUM:
                    $bit1 = true;
                    break;

                case ZipCompressionLevel::FAST:
                    $bit2 = true;
                    break;

                case ZipCompressionLevel::SUPER_FAST:
                    $bit1 = true;
                    $bit2 = true;
                    break;
                // default is ZipCompressionLevel::NORMAL
            }

            $this->generalPurposeBitFlags |= ($bit1 ? GeneralPurposeBitFlag::COMPRESSION_FLAG1 : 0);
            $this->generalPurposeBitFlags |= ($bit2 ? GeneralPurposeBitFlag::COMPRESSION_FLAG2 : 0);
        }
    }

    /**
     * Sets Unix permissions in a way that is understood by Info-Zip's
     * unzip command.
     *
     * @param int $mode mode an int value
     *
     * @return ZipEntry
     */
    public function setUnixMode($mode)
    {
        $mode = (int) $mode;
        $this->setExternalAttributes(
            ($mode << 16)
            // MS-DOS read-only attribute
            | (($mode & UnixStat::UNX_IWUSR) === 0 ? DosAttrs::DOS_HIDDEN : 0)
            // MS-DOS directory flag
            | ($this->isDirectory() ? DosAttrs::DOS_DIRECTORY : DosAttrs::DOS_ARCHIVE)
        );
        $this->createdOS = ZipPlatform::OS_UNIX;

        return $this;
    }

    /**
     * Unix permission.
     *
     * @return int the unix permissions
     */
    public function getUnixMode()
    {
        $mode = 0;

        if ($this->createdOS === ZipPlatform::OS_UNIX) {
            $mode = ($this->externalAttributes >> 16) & 0xFFFF;
        } elseif ($this->hasExtraField(AsiExtraField::HEADER_ID)) {
            /** @var AsiExtraField $asiExtraField */
            $asiExtraField = $this->getExtraField(AsiExtraField::HEADER_ID);
            $mode = $asiExtraField->getMode();
        }

        if ($mode > 0) {
            return $mode;
        }

        return $this->isDirectory ? 040755 : 0100644;
    }

    /**
     * Offset MUST be considered in decision about ZIP64 format - see
     * description of Data Descriptor in ZIP File Format Specification.
     *
     * @return bool
     */
    public function isZip64ExtensionsRequired()
    {
        return $this->compressedSize > ZipConstants::ZIP64_MAGIC
            || $this->uncompressedSize > ZipConstants::ZIP64_MAGIC;
    }

    /**
     * Returns true if this entry represents a unix symlink,
     * in which case the entry's content contains the target path
     * for the symlink.
     *
     * @return bool true if the entry represents a unix symlink,
     *              false otherwise
     */
    public function isUnixSymlink()
    {
        return ($this->getUnixMode() & UnixStat::UNX_IFMT) === UnixStat::UNX_IFLNK;
    }

    /**
     * @return \DateTimeInterface
     */
    public function getMTime()
    {
        /** @var NtfsExtraField|null $ntfsExtra */
        $ntfsExtra = $this->getExtraField(NtfsExtraField::HEADER_ID);

        if ($ntfsExtra !== null) {
            return $ntfsExtra->getModifyDateTime();
        }

        /** @var ExtendedTimestampExtraField|null $extendedExtra */
        $extendedExtra = $this->getExtraField(ExtendedTimestampExtraField::HEADER_ID);

        if ($extendedExtra !== null && ($mtime = $extendedExtra->getModifyDateTime()) !== null) {
            return $mtime;
        }

        /** @var OldUnixExtraField|null $oldUnixExtra */
        $oldUnixExtra = $this->getExtraField(OldUnixExtraField::HEADER_ID);

        if ($oldUnixExtra !== null && ($mtime = $oldUnixExtra->getModifyDateTime()) !== null) {
            return $mtime;
        }

        $timestamp = $this->getTime();

        try {
            return new \DateTimeImmutable('@' . $timestamp);
        } catch (\Exception $e) {
            throw new RuntimeException('Error create DateTime object with timestamp ' . $timestamp, 1, $e);
        }
    }

    /**
     * @return \DateTimeInterface|null
     */
    public function getATime()
    {
        /** @var NtfsExtraField|null $ntfsExtra */
        $ntfsExtra = $this->getExtraField(NtfsExtraField::HEADER_ID);

        if ($ntfsExtra !== null) {
            return $ntfsExtra->getAccessDateTime();
        }

        /** @var ExtendedTimestampExtraField|null $extendedExtra */
        $extendedExtra = $this->getExtraField(ExtendedTimestampExtraField::HEADER_ID);

        if ($extendedExtra !== null && ($atime = $extendedExtra->getAccessDateTime()) !== null) {
            return $atime;
        }

        /** @var OldUnixExtraField|null $oldUnixExtra */
        $oldUnixExtra = $this->getExtraField(OldUnixExtraField::HEADER_ID);

        if ($oldUnixExtra !== null) {
            return $oldUnixExtra->getAccessDateTime();
        }

        return null;
    }

    /**
     * @return \DateTimeInterface|null
     */
    public function getCTime()
    {
        /** @var NtfsExtraField|null $ntfsExtra */
        $ntfsExtra = $this->getExtraField(NtfsExtraField::HEADER_ID);

        if ($ntfsExtra !== null) {
            return $ntfsExtra->getCreateDateTime();
        }

        /** @var ExtendedTimestampExtraField|null $extendedExtra */
        $extendedExtra = $this->getExtraField(ExtendedTimestampExtraField::HEADER_ID);

        if ($extendedExtra !== null) {
            return $extendedExtra->getCreateDateTime();
        }

        return null;
    }

    public function __clone()
    {
        $this->cdExtraFields = clone $this->cdExtraFields;
        $this->localExtraFields = clone $this->localExtraFields;

        if ($this->data !== null) {
            $this->data = clone $this->data;
        }
    }
}
