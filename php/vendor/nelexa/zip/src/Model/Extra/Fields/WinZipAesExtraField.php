<?php

namespace PhpZip\Model\Extra\Fields;

use PhpZip\Constants\ZipCompressionMethod;
use PhpZip\Constants\ZipEncryptionMethod;
use PhpZip\Exception\InvalidArgumentException;
use PhpZip\Exception\ZipException;
use PhpZip\Exception\ZipUnsupportMethodException;
use PhpZip\Model\Extra\ZipExtraField;
use PhpZip\Model\ZipEntry;

/**
 * WinZip AES Extra Field.
 *
 * @see http://www.winzip.com/win/en/aes_tips.htm AES Coding Tips for Developers
 */
class WinZipAesExtraField implements ZipExtraField
{
    /** @var int Header id */
    const HEADER_ID = 0x9901;

    /**
     * @var int Data size (currently 7, but subject to possible increase
     *          in the future)
     */
    const DATA_SIZE = 7;

    /**
     * @var int The vendor ID field should always be set to the two ASCII
     *          characters "AE"
     */
    const VENDOR_ID = 0x4541; // 'A' | ('E' << 8)

    /**
     * @var int Entries of this type do include the standard ZIP CRC-32 value.
     *          For use with {@see WinZipAesExtraField::setVendorVersion()}.
     */
    const VERSION_AE1 = 1;

    /**
     * @var int Entries of this type do not include the standard ZIP CRC-32 value.
     *          For use with {@see WinZipAesExtraField::setVendorVersion().
     */
    const VERSION_AE2 = 2;

    /** @var int integer mode value indicating AES encryption 128-bit strength */
    const KEY_STRENGTH_128BIT = 0x01;

    /** @var int integer mode value indicating AES encryption 192-bit strength */
    const KEY_STRENGTH_192BIT = 0x02;

    /** @var int integer mode value indicating AES encryption 256-bit strength */
    const KEY_STRENGTH_256BIT = 0x03;

    /** @var int[] */
    private static $allowVendorVersions = [
        self::VERSION_AE1,
        self::VERSION_AE2,
    ];

    /** @var array<int, int> */
    private static $encryptionStrengths = [
        self::KEY_STRENGTH_128BIT => 128,
        self::KEY_STRENGTH_192BIT => 192,
        self::KEY_STRENGTH_256BIT => 256,
    ];

    /** @var array<int, int> */
    private static $MAP_KEY_STRENGTH_METHODS = [
        self::KEY_STRENGTH_128BIT => ZipEncryptionMethod::WINZIP_AES_128,
        self::KEY_STRENGTH_192BIT => ZipEncryptionMethod::WINZIP_AES_192,
        self::KEY_STRENGTH_256BIT => ZipEncryptionMethod::WINZIP_AES_256,
    ];

    /** @var int Integer version number specific to the zip vendor */
    private $vendorVersion = self::VERSION_AE1;

    /** @var int Integer mode value indicating AES encryption strength */
    private $keyStrength = self::KEY_STRENGTH_256BIT;

    /** @var int The actual compression method used to compress the file */
    private $compressionMethod;

    /**
     * @param int $vendorVersion     Integer version number specific to the zip vendor
     * @param int $keyStrength       Integer mode value indicating AES encryption strength
     * @param int $compressionMethod The actual compression method used to compress the file
     *
     * @throws ZipUnsupportMethodException
     */
    public function __construct($vendorVersion, $keyStrength, $compressionMethod)
    {
        $this->setVendorVersion($vendorVersion);
        $this->setKeyStrength($keyStrength);
        $this->setCompressionMethod($compressionMethod);
    }

    /**
     * @param ZipEntry $entry
     *
     * @throws ZipUnsupportMethodException
     *
     * @return WinZipAesExtraField
     */
    public static function create(ZipEntry $entry)
    {
        $keyStrength = array_search($entry->getEncryptionMethod(), self::$MAP_KEY_STRENGTH_METHODS, true);

        if ($keyStrength === false) {
            throw new InvalidArgumentException('Not support encryption method ' . $entry->getEncryptionMethod());
        }

        // WinZip 11 will continue to use AE-2, with no CRC, for very small files
        // of less than 20 bytes. It will also use AE-2 for files compressed in
        // BZIP2 format, because this format has internal integrity checks
        // equivalent to a CRC check built in.
        //
        // https://www.winzip.com/win/en/aes_info.html
        $vendorVersion = (
            $entry->getUncompressedSize() < 20 ||
            $entry->getCompressionMethod() === ZipCompressionMethod::BZIP2
        ) ?
            self::VERSION_AE2 :
            self::VERSION_AE1;

        $field = new self($vendorVersion, $keyStrength, $entry->getCompressionMethod());

        $entry->getLocalExtraFields()->add($field);
        $entry->getCdExtraFields()->add($field);

        return $field;
    }

    /**
     * Returns the Header ID (type) of this Extra Field.
     * The Header ID is an unsigned short integer (two bytes)
     * which must be constant during the life cycle of this object.
     *
     * @return int
     */
    public function getHeaderId()
    {
        return self::HEADER_ID;
    }

    /**
     * Populate data from this array as if it was in local file data.
     *
     * @param string        $buffer the buffer to read data from
     * @param ZipEntry|null $entry
     *
     * @throws ZipException on error
     *
     * @return WinZipAesExtraField
     */
    public static function unpackLocalFileData($buffer, ZipEntry $entry = null)
    {
        $size = \strlen($buffer);

        if ($size !== self::DATA_SIZE) {
            throw new ZipException(
                sprintf(
                    'WinZip AES Extra data invalid size: %d. Must be %d',
                    $size,
                    self::DATA_SIZE
                )
            );
        }

        $data = unpack('vvendorVersion/vvendorId/ckeyStrength/vcompressionMethod', $buffer);

        if ($data['vendorId'] !== self::VENDOR_ID) {
            throw new ZipException(
                sprintf(
                    'Vendor id invalid: %d. Must be %d',
                    $data['vendorId'],
                    self::VENDOR_ID
                )
            );
        }

        return new self(
            $data['vendorVersion'],
            $data['keyStrength'],
            $data['compressionMethod']
        );
    }

    /**
     * Populate data from this array as if it was in central directory data.
     *
     * @param string        $buffer the buffer to read data from
     * @param ZipEntry|null $entry
     *
     * @throws ZipException
     *
     * @return WinZipAesExtraField
     */
    public static function unpackCentralDirData($buffer, ZipEntry $entry = null)
    {
        return self::unpackLocalFileData($buffer, $entry);
    }

    /**
     * The actual data to put into local file data - without Header-ID
     * or length specifier.
     *
     * @return string the data
     */
    public function packLocalFileData()
    {
        return pack(
            'vvcv',
            $this->vendorVersion,
            self::VENDOR_ID,
            $this->keyStrength,
            $this->compressionMethod
        );
    }

    /**
     * The actual data to put into central directory - without Header-ID or
     * length specifier.
     *
     * @return string the data
     */
    public function packCentralDirData()
    {
        return $this->packLocalFileData();
    }

    /**
     * Returns the vendor version.
     *
     * @return int
     *
     * @see WinZipAesExtraField::VERSION_AE2
     * @see WinZipAesExtraField::VERSION_AE1
     */
    public function getVendorVersion()
    {
        return $this->vendorVersion;
    }

    /**
     * Sets the vendor version.
     *
     * @param int $vendorVersion the vendor version
     *
     * @see    WinZipAesExtraField::VERSION_AE2
     * @see    WinZipAesExtraField::VERSION_AE1
     */
    public function setVendorVersion($vendorVersion)
    {
        $vendorVersion = (int) $vendorVersion;

        if (!\in_array($vendorVersion, self::$allowVendorVersions, true)) {
            throw new InvalidArgumentException(
                sprintf(
                    'Unsupport WinZip AES vendor version: %d',
                    $vendorVersion
                )
            );
        }
        $this->vendorVersion = $vendorVersion;
    }

    /**
     * Returns vendor id.
     *
     * @return int
     */
    public function getVendorId()
    {
        return self::VENDOR_ID;
    }

    /**
     * @return int
     */
    public function getKeyStrength()
    {
        return $this->keyStrength;
    }

    /**
     * Set key strength.
     *
     * @param int $keyStrength
     */
    public function setKeyStrength($keyStrength)
    {
        $keyStrength = (int) $keyStrength;

        if (!isset(self::$encryptionStrengths[$keyStrength])) {
            throw new InvalidArgumentException(
                sprintf(
                    'Key strength %d not support value. Allow values: %s',
                    $keyStrength,
                    implode(', ', array_keys(self::$encryptionStrengths))
                )
            );
        }
        $this->keyStrength = $keyStrength;
    }

    /**
     * @return int
     */
    public function getCompressionMethod()
    {
        return $this->compressionMethod;
    }

    /**
     * @param int $compressionMethod
     *
     * @throws ZipUnsupportMethodException
     */
    public function setCompressionMethod($compressionMethod)
    {
        $compressionMethod = (int) $compressionMethod;
        ZipCompressionMethod::checkSupport($compressionMethod);
        $this->compressionMethod = $compressionMethod;
    }

    /**
     * @return int
     */
    public function getEncryptionStrength()
    {
        return self::$encryptionStrengths[$this->keyStrength];
    }

    /**
     * @return int
     */
    public function getEncryptionMethod()
    {
        $keyStrength = $this->getKeyStrength();

        if (!isset(self::$MAP_KEY_STRENGTH_METHODS[$keyStrength])) {
            throw new InvalidArgumentException('Invalid encryption method');
        }

        return self::$MAP_KEY_STRENGTH_METHODS[$keyStrength];
    }

    /**
     * @return bool
     */
    public function isV1()
    {
        return $this->vendorVersion === self::VERSION_AE1;
    }

    /**
     * @return bool
     */
    public function isV2()
    {
        return $this->vendorVersion === self::VERSION_AE2;
    }

    /**
     * @return int
     */
    public function getSaltSize()
    {
        return (int) ($this->getEncryptionStrength() / 8 / 2);
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return sprintf(
            '0x%04x WINZIP AES: VendorVersion=%d KeyStrength=0x%02x CompressionMethod=%s',
            __CLASS__,
            $this->vendorVersion,
            $this->keyStrength,
            $this->compressionMethod
        );
    }
}
