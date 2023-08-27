<?php

namespace PhpZip\Model\Extra;

use PhpZip\Model\ZipEntry;

/**
 * Extra Field in a Local or Central Header of a ZIP archive.
 * It defines the common properties of all Extra Fields and how to
 * serialize/unserialize them to/from byte arrays.
 */
interface ZipExtraField
{
    /**
     * Returns the Header ID (type) of this Extra Field.
     * The Header ID is an unsigned short integer (two bytes)
     * which must be constant during the life cycle of this object.
     *
     * @return int
     */
    public function getHeaderId();

    /**
     * Populate data from this array as if it was in local file data.
     *
     * @param string        $buffer the buffer to read data from
     * @param ZipEntry|null $entry
     *
     * @return static
     */
    public static function unpackLocalFileData($buffer, ZipEntry $entry = null);

    /**
     * Populate data from this array as if it was in central directory data.
     *
     * @param string        $buffer the buffer to read data from
     * @param ZipEntry|null $entry
     *
     * @return static
     */
    public static function unpackCentralDirData($buffer, ZipEntry $entry = null);

    /**
     * The actual data to put into local file data - without Header-ID
     * or length specifier.
     *
     * @return string the data
     */
    public function packLocalFileData();

    /**
     * The actual data to put into central directory - without Header-ID or
     * length specifier.
     *
     * @return string the data
     */
    public function packCentralDirData();

    /**
     * @return string
     */
    public function __toString();
}
