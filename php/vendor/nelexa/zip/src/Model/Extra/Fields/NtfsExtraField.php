<?php

namespace PhpZip\Model\Extra\Fields;

use PhpZip\Exception\InvalidArgumentException;
use PhpZip\Exception\ZipException;
use PhpZip\Model\Extra\ZipExtraField;
use PhpZip\Model\ZipEntry;
use PhpZip\Util\PackUtil;

/**
 * NTFS Extra Field.
 *
 * @see     https://pkware.cachefly.net/webdocs/casestudies/APPNOTE.TXT .ZIP File Format Specification
 *
 * @license MIT
 */
class NtfsExtraField implements ZipExtraField
{
    /** @var int Header id */
    const HEADER_ID = 0x000a;

    /** @var int Tag ID */
    const TIME_ATTR_TAG = 0x0001;

    /** @var int Attribute size */
    const TIME_ATTR_SIZE = 24; // 3 * 8

    /**
     * @var int A file time is a 64-bit value that represents the number of
     *          100-nanosecond intervals that have elapsed since 12:00
     *          A.M. January 1, 1601 Coordinated Universal Time (UTC).
     *          this is the offset of Windows time 0 to Unix epoch in 100-nanosecond intervals.
     */
    const EPOCH_OFFSET = -116444736000000000;

    /** @var int Modify ntfs time */
    private $modifyNtfsTime;

    /** @var int Access ntfs time */
    private $accessNtfsTime;

    /** @var int Create ntfs time */
    private $createNtfsTime;

    /**
     * @param int $modifyNtfsTime
     * @param int $accessNtfsTime
     * @param int $createNtfsTime
     */
    public function __construct($modifyNtfsTime, $accessNtfsTime, $createNtfsTime)
    {
        $this->modifyNtfsTime = (int) $modifyNtfsTime;
        $this->accessNtfsTime = (int) $accessNtfsTime;
        $this->createNtfsTime = (int) $createNtfsTime;
    }

    /**
     * @param \DateTimeInterface $modifyDateTime
     * @param \DateTimeInterface $accessDateTime
     * @param \DateTimeInterface $createNtfsTime
     *
     * @return NtfsExtraField
     */
    public static function create(
        \DateTimeInterface $modifyDateTime,
        \DateTimeInterface $accessDateTime,
        \DateTimeInterface $createNtfsTime
    ) {
        return new self(
            self::dateTimeToNtfsTime($modifyDateTime),
            self::dateTimeToNtfsTime($accessDateTime),
            self::dateTimeToNtfsTime($createNtfsTime)
        );
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
     * @throws ZipException
     *
     * @return NtfsExtraField
     */
    public static function unpackLocalFileData($buffer, ZipEntry $entry = null)
    {
        if (\PHP_INT_SIZE === 4) {
            throw new ZipException('not supported for php-32bit');
        }

        $buffer = substr($buffer, 4);

        $modifyTime = 0;
        $accessTime = 0;
        $createTime = 0;

        while ($buffer || $buffer !== '') {
            $unpack = unpack('vtag/vsizeAttr', $buffer);

            if ($unpack['tag'] === self::TIME_ATTR_TAG && $unpack['sizeAttr'] === self::TIME_ATTR_SIZE) {
                // refactoring will be needed when php 5.5 support ends
                $modifyTime = PackUtil::unpackLongLE(substr($buffer, 4, 8));
                $accessTime = PackUtil::unpackLongLE(substr($buffer, 12, 8));
                $createTime = PackUtil::unpackLongLE(substr($buffer, 20, 8));

                break;
            }
            $buffer = substr($buffer, 4 + $unpack['sizeAttr']);
        }

        return new self($modifyTime, $accessTime, $createTime);
    }

    /**
     * Populate data from this array as if it was in central directory data.
     *
     * @param string        $buffer the buffer to read data from
     * @param ZipEntry|null $entry
     *
     * @throws ZipException
     *
     * @return NtfsExtraField
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
        $data = pack('Vvv', 0, self::TIME_ATTR_TAG, self::TIME_ATTR_SIZE);
        // refactoring will be needed when php 5.5 support ends
        $data .= PackUtil::packLongLE($this->modifyNtfsTime);
        $data .= PackUtil::packLongLE($this->accessNtfsTime);
        $data .= PackUtil::packLongLE($this->createNtfsTime);

        return $data;
    }

    /**
     * @return int
     */
    public function getModifyNtfsTime()
    {
        return $this->modifyNtfsTime;
    }

    /**
     * @param int $modifyNtfsTime
     */
    public function setModifyNtfsTime($modifyNtfsTime)
    {
        $this->modifyNtfsTime = (int) $modifyNtfsTime;
    }

    /**
     * @return int
     */
    public function getAccessNtfsTime()
    {
        return $this->accessNtfsTime;
    }

    /**
     * @param int $accessNtfsTime
     */
    public function setAccessNtfsTime($accessNtfsTime)
    {
        $this->accessNtfsTime = (int) $accessNtfsTime;
    }

    /**
     * @return int
     */
    public function getCreateNtfsTime()
    {
        return $this->createNtfsTime;
    }

    /**
     * @param int $createNtfsTime
     */
    public function setCreateNtfsTime($createNtfsTime)
    {
        $this->createNtfsTime = (int) $createNtfsTime;
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
     * @return \DateTimeInterface
     */
    public function getModifyDateTime()
    {
        return self::ntfsTimeToDateTime($this->modifyNtfsTime);
    }

    /**
     * @param \DateTimeInterface $modifyTime
     */
    public function setModifyDateTime(\DateTimeInterface $modifyTime)
    {
        $this->modifyNtfsTime = self::dateTimeToNtfsTime($modifyTime);
    }

    /**
     * @return \DateTimeInterface
     */
    public function getAccessDateTime()
    {
        return self::ntfsTimeToDateTime($this->accessNtfsTime);
    }

    /**
     * @param \DateTimeInterface $accessTime
     */
    public function setAccessDateTime(\DateTimeInterface $accessTime)
    {
        $this->accessNtfsTime = self::dateTimeToNtfsTime($accessTime);
    }

    /**
     * @return \DateTimeInterface
     */
    public function getCreateDateTime()
    {
        return self::ntfsTimeToDateTime($this->createNtfsTime);
    }

    /**
     * @param \DateTimeInterface $createTime
     */
    public function setCreateDateTime(\DateTimeInterface $createTime)
    {
        $this->createNtfsTime = self::dateTimeToNtfsTime($createTime);
    }

    /**
     * @param float $timestamp Float timestamp
     *
     * @return int
     */
    public static function timestampToNtfsTime($timestamp)
    {
        return (int) (((float) $timestamp * 10000000) - self::EPOCH_OFFSET);
    }

    /**
     * @param \DateTimeInterface $dateTime
     *
     * @return int
     */
    public static function dateTimeToNtfsTime(\DateTimeInterface $dateTime)
    {
        return self::timestampToNtfsTime((float) $dateTime->format('U.u'));
    }

    /**
     * @param int $ntfsTime
     *
     * @return float Float unix timestamp
     */
    public static function ntfsTimeToTimestamp($ntfsTime)
    {
        return (float) (($ntfsTime + self::EPOCH_OFFSET) / 10000000);
    }

    /**
     * @param int $ntfsTime
     *
     * @return \DateTimeInterface
     */
    public static function ntfsTimeToDateTime($ntfsTime)
    {
        $timestamp = self::ntfsTimeToTimestamp($ntfsTime);
        $dateTime = \DateTimeImmutable::createFromFormat('U.u', sprintf('%.6f', $timestamp));

        if ($dateTime === false) {
            throw new InvalidArgumentException('Cannot create date/time object for timestamp ' . $timestamp);
        }

        return $dateTime;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        $args = [self::HEADER_ID];
        $format = '0x%04x NtfsExtra:';

        if ($this->modifyNtfsTime !== 0) {
            $format .= ' Modify:[%s]';
            $args[] = $this->getModifyDateTime()->format(\DATE_ATOM);
        }

        if ($this->accessNtfsTime !== 0) {
            $format .= ' Access:[%s]';
            $args[] = $this->getAccessDateTime()->format(\DATE_ATOM);
        }

        if ($this->createNtfsTime !== 0) {
            $format .= ' Create:[%s]';
            $args[] = $this->getCreateDateTime()->format(\DATE_ATOM);
        }

        return vsprintf($format, $args);
    }
}
