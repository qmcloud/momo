<?php

namespace PhpZip\Model\Extra\Fields;

use PhpZip\Constants\ZipConstants;
use PhpZip\Exception\RuntimeException;
use PhpZip\Exception\ZipException;
use PhpZip\Model\Extra\ZipExtraField;
use PhpZip\Model\ZipEntry;
use PhpZip\Util\PackUtil;

/**
 * ZIP64 Extra Field.
 *
 * @see https://pkware.cachefly.net/webdocs/casestudies/APPNOTE.TXT .ZIP File Format Specification
 */
class Zip64ExtraField implements ZipExtraField
{
    /** @var int The Header ID for a ZIP64 Extended Information Extra Field. */
    const HEADER_ID = 0x0001;

    /** @var int|null */
    private $uncompressedSize;

    /** @var int|null */
    private $compressedSize;

    /** @var int|null */
    private $localHeaderOffset;

    /** @var int|null */
    private $diskStart;

    /**
     * Zip64ExtraField constructor.
     *
     * @param int|null $uncompressedSize
     * @param int|null $compressedSize
     * @param int|null $localHeaderOffset
     * @param int|null $diskStart
     */
    public function __construct(
        $uncompressedSize = null,
        $compressedSize = null,
        $localHeaderOffset = null,
        $diskStart = null
    ) {
        $this->uncompressedSize = $uncompressedSize;
        $this->compressedSize = $compressedSize;
        $this->localHeaderOffset = $localHeaderOffset;
        $this->diskStart = $diskStart;
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
     * @return Zip64ExtraField
     */
    public static function unpackLocalFileData($buffer, ZipEntry $entry = null)
    {
        $length = \strlen($buffer);

        if ($length === 0) {
            // no local file data at all, may happen if an archive
            // only holds a ZIP64 extended information extra field
            // inside the central directory but not inside the local
            // file header
            return new self();
        }

        if ($length < 16) {
            throw new ZipException(
                'Zip64 extended information must contain both size values in the local file header.'
            );
        }

        $uncompressedSize = PackUtil::unpackLongLE(substr($buffer, 0, 8));
        $compressedSize = PackUtil::unpackLongLE(substr($buffer, 8, 8));

        return new self($uncompressedSize, $compressedSize);
    }

    /**
     * Populate data from this array as if it was in central directory data.
     *
     * @param string        $buffer the buffer to read data from
     * @param ZipEntry|null $entry
     *
     * @throws ZipException
     *
     * @return Zip64ExtraField
     */
    public static function unpackCentralDirData($buffer, ZipEntry $entry = null)
    {
        if ($entry === null) {
            throw new RuntimeException('zipEntry is null');
        }

        $length = \strlen($buffer);
        $remaining = $length;

        $uncompressedSize = null;
        $compressedSize = null;
        $localHeaderOffset = null;
        $diskStart = null;

        if ($entry->getUncompressedSize() === ZipConstants::ZIP64_MAGIC) {
            if ($remaining < 8) {
                throw new ZipException('ZIP64 extension corrupt (no uncompressed size).');
            }
            $uncompressedSize = PackUtil::unpackLongLE(substr($buffer, $length - $remaining, 8));
            $remaining -= 8;
        }

        if ($entry->getCompressedSize() === ZipConstants::ZIP64_MAGIC) {
            if ($remaining < 8) {
                throw new ZipException('ZIP64 extension corrupt (no compressed size).');
            }
            $compressedSize = PackUtil::unpackLongLE(substr($buffer, $length - $remaining, 8));
            $remaining -= 8;
        }

        if ($entry->getLocalHeaderOffset() === ZipConstants::ZIP64_MAGIC) {
            if ($remaining < 8) {
                throw new ZipException('ZIP64 extension corrupt (no relative local header offset).');
            }
            $localHeaderOffset = PackUtil::unpackLongLE(substr($buffer, $length - $remaining, 8));
            $remaining -= 8;
        }

        if ($remaining === 4) {
            $diskStart = unpack('V', substr($buffer, $length - $remaining, 4))[1];
        }

        return new self($uncompressedSize, $compressedSize, $localHeaderOffset, $diskStart);
    }

    /**
     * The actual data to put into local file data - without Header-ID
     * or length specifier.
     *
     * @return string the data
     */
    public function packLocalFileData()
    {
        if ($this->uncompressedSize !== null || $this->compressedSize !== null) {
            if ($this->uncompressedSize === null || $this->compressedSize === null) {
                throw new \InvalidArgumentException(
                    'Zip64 extended information must contain both size values in the local file header.'
                );
            }

            return $this->packSizes();
        }

        return '';
    }

    /**
     * @return string
     */
    private function packSizes()
    {
        $data = '';

        if ($this->uncompressedSize !== null) {
            $data .= PackUtil::packLongLE($this->uncompressedSize);
        }

        if ($this->compressedSize !== null) {
            $data .= PackUtil::packLongLE($this->compressedSize);
        }

        return $data;
    }

    /**
     * The actual data to put into central directory - without Header-ID or
     * length specifier.
     *
     * @return string the data
     */
    public function packCentralDirData()
    {
        $data = $this->packSizes();

        if ($this->localHeaderOffset !== null) {
            $data .= PackUtil::packLongLE($this->localHeaderOffset);
        }

        if ($this->diskStart !== null) {
            $data .= pack('V', $this->diskStart);
        }

        return $data;
    }

    /**
     * @return int|null
     */
    public function getUncompressedSize()
    {
        return $this->uncompressedSize;
    }

    /**
     * @param int|null $uncompressedSize
     */
    public function setUncompressedSize($uncompressedSize)
    {
        $this->uncompressedSize = $uncompressedSize;
    }

    /**
     * @return int|null
     */
    public function getCompressedSize()
    {
        return $this->compressedSize;
    }

    /**
     * @param int|null $compressedSize
     */
    public function setCompressedSize($compressedSize)
    {
        $this->compressedSize = $compressedSize;
    }

    /**
     * @return int|null
     */
    public function getLocalHeaderOffset()
    {
        return $this->localHeaderOffset;
    }

    /**
     * @param int|null $localHeaderOffset
     */
    public function setLocalHeaderOffset($localHeaderOffset)
    {
        $this->localHeaderOffset = $localHeaderOffset;
    }

    /**
     * @return int|null
     */
    public function getDiskStart()
    {
        return $this->diskStart;
    }

    /**
     * @param int|null $diskStart
     */
    public function setDiskStart($diskStart)
    {
        $this->diskStart = $diskStart;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        $args = [self::HEADER_ID];
        $format = '0x%04x ZIP64: ';
        $formats = [];

        if ($this->uncompressedSize !== null) {
            $formats[] = 'SIZE=%d';
            $args[] = $this->uncompressedSize;
        }

        if ($this->compressedSize !== null) {
            $formats[] = 'COMP_SIZE=%d';
            $args[] = $this->compressedSize;
        }

        if ($this->localHeaderOffset !== null) {
            $formats[] = 'OFFSET=%d';
            $args[] = $this->localHeaderOffset;
        }

        if ($this->diskStart !== null) {
            $formats[] = 'DISK_START=%d';
            $args[] = $this->diskStart;
        }
        $format .= implode(' ', $formats);

        return vsprintf($format, $args);
    }
}
