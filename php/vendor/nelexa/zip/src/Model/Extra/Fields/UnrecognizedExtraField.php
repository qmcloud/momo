<?php

namespace PhpZip\Model\Extra\Fields;

use PhpZip\Exception\RuntimeException;
use PhpZip\Model\Extra\ZipExtraField;
use PhpZip\Model\ZipEntry;

/**
 * Simple placeholder for all those extra fields we don't want to deal with.
 */
class UnrecognizedExtraField implements ZipExtraField
{
    /** @var int */
    private $headerId;

    /** @var string extra field data without Header-ID or length specifier */
    private $data;

    /**
     * UnrecognizedExtraField constructor.
     *
     * @param int    $headerId
     * @param string $data
     */
    public function __construct($headerId, $data)
    {
        $this->headerId = (int) $headerId;
        $this->data = (string) $data;
    }

    /**
     * @param int $headerId
     */
    public function setHeaderId($headerId)
    {
        $this->headerId = $headerId;
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
        return $this->headerId;
    }

    /**
     * Populate data from this array as if it was in local file data.
     *
     * @param string        $buffer the buffer to read data from
     * @param ZipEntry|null $entry
     */
    public static function unpackLocalFileData($buffer, ZipEntry $entry = null)
    {
        throw new RuntimeException('Unsupport parse');
    }

    /**
     * Populate data from this array as if it was in central directory data.
     *
     * @param string        $buffer the buffer to read data from
     * @param ZipEntry|null $entry
     */
    public static function unpackCentralDirData($buffer, ZipEntry $entry = null)
    {
        throw new RuntimeException('Unsupport parse');
    }

    /**
     * {@inheritdoc}
     */
    public function packLocalFileData()
    {
        return $this->data;
    }

    /**
     * {@inheritdoc}
     */
    public function packCentralDirData()
    {
        return $this->data;
    }

    /**
     * @return string
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @param string $data
     */
    public function setData($data)
    {
        $this->data = (string) $data;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        $args = [$this->headerId, $this->data];
        $format = '0x%04x Unrecognized Extra Field: "%s"';

        return vsprintf($format, $args);
    }
}
