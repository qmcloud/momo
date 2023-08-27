<?php

namespace PhpZip\Model\Extra\Fields;

use PhpZip\Exception\ZipException;
use PhpZip\Model\Extra\ZipExtraField;
use PhpZip\Model\ZipEntry;

/**
 * Apk Alignment Extra Field.
 *
 * @see https://android.googlesource.com/platform/tools/apksig/+/master/src/main/java/com/android/apksig/ApkSigner.java
 * @see https://developer.android.com/studio/command-line/zipalign
 */
class ApkAlignmentExtraField implements ZipExtraField
{
    /**
     * @var int Extensible data block/field header ID used for storing
     *          information about alignment of uncompressed entries as
     *          well as for aligning the entries's data. See ZIP
     *          appnote.txt section 4.5 Extensible data fields.
     */
    const HEADER_ID = 0xd935;

    /**
     * @var int minimum size (in bytes) of the extensible data block/field used
     *          for alignment of uncompressed entries
     */
    const MIN_SIZE = 6;

    /** @var int */
    const ALIGNMENT_BYTES = 4;

    /** @var int */
    const COMMON_PAGE_ALIGNMENT_BYTES = 4096;

    /** @var int */
    private $multiple;

    /** @var int */
    private $padding;

    /**
     * @param int $multiple
     * @param int $padding
     */
    public function __construct($multiple, $padding)
    {
        $this->multiple = $multiple;
        $this->padding = $padding;
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
     * @return int
     */
    public function getMultiple()
    {
        return $this->multiple;
    }

    /**
     * @return int
     */
    public function getPadding()
    {
        return $this->padding;
    }

    /**
     * @param int $multiple
     */
    public function setMultiple($multiple)
    {
        $this->multiple = (int) $multiple;
    }

    /**
     * @param int $padding
     */
    public function setPadding($padding)
    {
        $this->padding = (int) $padding;
    }

    /**
     * Populate data from this array as if it was in local file data.
     *
     * @param string        $buffer the buffer to read data from
     * @param ZipEntry|null $entry
     *
     * @throws ZipException
     *
     * @return ApkAlignmentExtraField
     */
    public static function unpackLocalFileData($buffer, ZipEntry $entry = null)
    {
        $length = \strlen($buffer);

        if ($length < 2) {
            // This is APK alignment field.
            // FORMAT:
            //  * uint16 alignment multiple (in bytes)
            //  * remaining bytes -- padding to achieve alignment of data which starts after
            //    the extra field
            throw new ZipException(
                'Minimum 6 bytes of the extensible data block/field used for alignment of uncompressed entries.'
            );
        }
        $multiple = unpack('v', $buffer)[1];
        $padding = $length - 2;

        return new self($multiple, $padding);
    }

    /**
     * Populate data from this array as if it was in central directory data.
     *
     * @param string        $buffer the buffer to read data from
     * @param ZipEntry|null $entry
     *
     * @throws ZipException on error
     *
     * @return ApkAlignmentExtraField
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
        return pack('vx' . $this->padding, $this->multiple);
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
     * @return string
     */
    public function __toString()
    {
        return sprintf(
            '0x%04x APK Alignment: Multiple=%d Padding=%d',
            self::HEADER_ID,
            $this->multiple,
            $this->padding
        );
    }
}
