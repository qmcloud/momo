<?php

namespace PhpZip\Model\Extra\Fields;

use PhpZip\Model\Extra\ZipExtraField;
use PhpZip\Model\ZipEntry;

/**
 * Info-ZIP Unix Extra Field (type 1):
 * ==================================.
 *
 * The following is the layout of the old Info-ZIP extra block for
 * Unix.  It has been replaced by the extended-timestamp extra block
 * (0x5455) and the Unix type 2 extra block (0x7855).
 * (Last Revision 19970118)
 *
 * Local-header version:
 *
 * Value         Size        Description
 * -----         ----        -----------
 * (Unix1) 0x5855        Short       tag for this extra block type ("UX")
 * TSize         Short       total data size for this block
 * AcTime        Long        time of last access (UTC/GMT)
 * ModTime       Long        time of last modification (UTC/GMT)
 * UID           Short       Unix user ID (optional)
 * GID           Short       Unix group ID (optional)
 *
 * Central-header version:
 *
 * Value         Size        Description
 * -----         ----        -----------
 * (Unix1) 0x5855        Short       tag for this extra block type ("UX")
 * TSize         Short       total data size for this block
 * AcTime        Long        time of last access (GMT/UTC)
 * ModTime       Long        time of last modification (GMT/UTC)
 *
 * The file access and modification times are in standard Unix signed-
 * long format, indicating the number of seconds since 1 January 1970
 * 00:00:00.  The times are relative to Coordinated Universal Time
 * (UTC), also sometimes referred to as Greenwich Mean Time (GMT).  To
 * convert to local time, the software must know the local timezone
 * offset from UTC/GMT.  The modification time may be used by non-Unix
 * systems to support inter-timezone freshening and updating of zip
 * archives.
 *
 * The local-header extra block may optionally contain UID and GID
 * info for the file.  The local-header TSize value is the only
 * indication of this.  Note that Unix UIDs and GIDs are usually
 * specific to a particular machine, and they generally require root
 * access to restore.
 *
 * This extra field type is obsolete, but it has been in use since
 * mid-1994. Therefore future archiving software should continue to
 * support it.
 */
class OldUnixExtraField implements ZipExtraField
{
    /** @var int Header id */
    const HEADER_ID = 0x5855;

    /** @var int|null Access timestamp */
    private $accessTime;

    /** @var int|null Modify timestamp */
    private $modifyTime;

    /** @var int|null User id */
    private $uid;

    /** @var int|null Group id */
    private $gid;

    /**
     * @param int|null $accessTime
     * @param int|null $modifyTime
     * @param int|null $uid
     * @param int|null $gid
     */
    public function __construct($accessTime, $modifyTime, $uid, $gid)
    {
        $this->accessTime = $accessTime;
        $this->modifyTime = $modifyTime;
        $this->uid = $uid;
        $this->gid = $gid;
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
     * @return OldUnixExtraField
     */
    public static function unpackLocalFileData($buffer, ZipEntry $entry = null)
    {
        $length = \strlen($buffer);

        $accessTime = $modifyTime = $uid = $gid = null;

        if ($length >= 4) {
            $accessTime = unpack('V', $buffer)[1];
        }

        if ($length >= 8) {
            $modifyTime = unpack('V', substr($buffer, 4, 4))[1];
        }

        if ($length >= 10) {
            $uid = unpack('v', substr($buffer, 8, 2))[1];
        }

        if ($length >= 12) {
            $gid = unpack('v', substr($buffer, 10, 2))[1];
        }

        return new self($accessTime, $modifyTime, $uid, $gid);
    }

    /**
     * Populate data from this array as if it was in central directory data.
     *
     * @param string        $buffer the buffer to read data from
     * @param ZipEntry|null $entry
     *
     * @return OldUnixExtraField
     */
    public static function unpackCentralDirData($buffer, ZipEntry $entry = null)
    {
        $length = \strlen($buffer);

        $accessTime = $modifyTime = null;

        if ($length >= 4) {
            $accessTime = unpack('V', $buffer)[1];
        }

        if ($length >= 8) {
            $modifyTime = unpack('V', substr($buffer, 4, 4))[1];
        }

        return new self($accessTime, $modifyTime, null, null);
    }

    /**
     * The actual data to put into local file data - without Header-ID
     * or length specifier.
     *
     * @return string the data
     */
    public function packLocalFileData()
    {
        $data = '';

        if ($this->accessTime !== null) {
            $data .= pack('V', $this->accessTime);

            if ($this->modifyTime !== null) {
                $data .= pack('V', $this->modifyTime);

                if ($this->uid !== null) {
                    $data .= pack('v', $this->uid);

                    if ($this->gid !== null) {
                        $data .= pack('v', $this->gid);
                    }
                }
            }
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
        $data = '';

        if ($this->accessTime !== null) {
            $data .= pack('V', $this->accessTime);

            if ($this->modifyTime !== null) {
                $data .= pack('V', $this->modifyTime);
            }
        }

        return $data;
    }

    /**
     * @return int|null
     */
    public function getAccessTime()
    {
        return $this->accessTime;
    }

    /**
     * @param int|null $accessTime
     */
    public function setAccessTime($accessTime)
    {
        $this->accessTime = $accessTime;
    }

    /**
     * @return \DateTimeInterface|null
     */
    public function getAccessDateTime()
    {
        try {
            return $this->accessTime === null ? null :
                new \DateTimeImmutable('@' . $this->accessTime);
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * @return int|null
     */
    public function getModifyTime()
    {
        return $this->modifyTime;
    }

    /**
     * @param int|null $modifyTime
     */
    public function setModifyTime($modifyTime)
    {
        $this->modifyTime = $modifyTime;
    }

    /**
     * @return \DateTimeInterface|null
     */
    public function getModifyDateTime()
    {
        try {
            return $this->modifyTime === null ? null :
                new \DateTimeImmutable('@' . $this->modifyTime);
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * @return int|null
     */
    public function getUid()
    {
        return $this->uid;
    }

    /**
     * @param int|null $uid
     */
    public function setUid($uid)
    {
        $this->uid = $uid;
    }

    /**
     * @return int|null
     */
    public function getGid()
    {
        return $this->gid;
    }

    /**
     * @param int|null $gid
     */
    public function setGid($gid)
    {
        $this->gid = $gid;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        $args = [self::HEADER_ID];
        $format = '0x%04x OldUnix:';

        if (($modifyTime = $this->getModifyDateTime()) !== null) {
            $format .= ' Modify:[%s]';
            $args[] = $modifyTime->format(\DATE_ATOM);
        }

        if (($accessTime = $this->getAccessDateTime()) !== null) {
            $format .= ' Access:[%s]';
            $args[] = $accessTime->format(\DATE_ATOM);
        }

        if ($this->uid !== null) {
            $format .= ' UID=%d';
            $args[] = $this->uid;
        }

        if ($this->gid !== null) {
            $format .= ' GID=%d';
            $args[] = $this->gid;
        }

        return vsprintf($format, $args);
    }
}
