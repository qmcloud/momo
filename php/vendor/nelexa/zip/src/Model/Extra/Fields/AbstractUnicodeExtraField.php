<?php

namespace PhpZip\Model\Extra\Fields;

use PhpZip\Exception\ZipException;
use PhpZip\Model\Extra\ZipExtraField;
use PhpZip\Model\ZipEntry;

/**
 * A common base class for Unicode extra information extra fields.
 */
abstract class AbstractUnicodeExtraField implements ZipExtraField
{
    const DEFAULT_VERSION = 0x01;

    /** @var int */
    private $crc32;

    /** @var string */
    private $unicodeValue;

    /**
     * @param int    $crc32
     * @param string $unicodeValue
     */
    public function __construct($crc32, $unicodeValue)
    {
        $this->crc32 = (int) $crc32;
        $this->unicodeValue = (string) $unicodeValue;
    }

    /**
     * @return int the CRC32 checksum of the filename or comment as
     *             encoded in the central directory of the zip file
     */
    public function getCrc32()
    {
        return $this->crc32;
    }

    /**
     * @param int $crc32
     */
    public function setCrc32($crc32)
    {
        $this->crc32 = (int) $crc32;
    }

    /**
     * @return string
     */
    public function getUnicodeValue()
    {
        return $this->unicodeValue;
    }

    /**
     * @param string $unicodeValue the UTF-8 encoded name to set
     */
    public function setUnicodeValue($unicodeValue)
    {
        $this->unicodeValue = $unicodeValue;
    }

    /**
     * Populate data from this array as if it was in local file data.
     *
     * @param string        $buffer the buffer to read data from
     * @param ZipEntry|null $entry
     *
     * @throws ZipException on error
     *
     * @return static
     */
    public static function unpackLocalFileData($buffer, ZipEntry $entry = null)
    {
        if (\strlen($buffer) < 5) {
            throw new ZipException('Unicode path extra data must have at least 5 bytes.');
        }

        $data = unpack('Cversion/Vcrc32', $buffer);

        if ($data['version'] !== self::DEFAULT_VERSION) {
            throw new ZipException(sprintf('Unsupported version [%d] for Unicode path extra data.', $data['version']));
        }

        $unicodeValue = substr($buffer, 5);

        return new static($data['crc32'], $unicodeValue);
    }

    /**
     * Populate data from this array as if it was in central directory data.
     *
     * @param string        $buffer the buffer to read data from
     * @param ZipEntry|null $entry
     *
     * @throws ZipException on error
     *
     * @return static
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
            'CV',
            self::DEFAULT_VERSION,
            $this->crc32
        ) .
            $this->unicodeValue;
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
}
