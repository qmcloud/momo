<?php

namespace PhpZip\Model\Extra\Fields;

use PhpZip\Constants\UnixStat;
use PhpZip\Exception\Crc32Exception;
use PhpZip\Model\Extra\ZipExtraField;
use PhpZip\Model\ZipEntry;

/**
 * ASi Unix Extra Field:
 * ====================.
 *
 * The following is the layout of the ASi extra block for Unix.  The
 * local-header and central-header versions are identical.
 * (Last Revision 19960916)
 *
 * Value         Size        Description
 * -----         ----        -----------
 * (Unix3) 0x756e        Short       tag for this extra block type ("nu")
 * TSize         Short       total data size for this block
 * CRC           Long        CRC-32 of the remaining data
 * Mode          Short       file permissions
 * SizDev        Long        symlink'd size OR major/minor dev num
 * UID           Short       user ID
 * GID           Short       group ID
 * (var.)        variable    symbolic link filename
 *
 * Mode is the standard Unix st_mode field from struct stat, containing
 * user/group/other permissions, setuid/setgid and symlink info, etc.
 *
 * If Mode indicates that this file is a symbolic link, SizDev is the
 * size of the file to which the link points.  Otherwise, if the file
 * is a device, SizDev contains the standard Unix st_rdev field from
 * struct stat (includes the major and minor numbers of the device).
 * SizDev is undefined in other cases.
 *
 * If Mode indicates that the file is a symbolic link, the final field
 * will be the name of the file to which the link points.  The file-
 * name length can be inferred from TSize.
 *
 * [Note that TSize may incorrectly refer to the data size not counting
 * the CRC; i.e., it may be four bytes too small.]
 *
 * @see ftp://ftp.info-zip.org/pub/infozip/doc/appnote-iz-latest.zip Info-ZIP version Specification
 */
class AsiExtraField implements ZipExtraField
{
    /** @var int Header id */
    const HEADER_ID = 0x756e;

    const USER_GID_PID = 1000;

    /** Bits used for permissions (and sticky bit). */
    const PERM_MASK = 07777;

    /** @var int Standard Unix stat(2) file mode. */
    private $mode;

    /** @var int User ID. */
    private $uid;

    /** @var int Group ID. */
    private $gid;

    /**
     * @var string File this entry points to, if it is a symbolic link.
     *             Empty string - if entry is not a symbolic link.
     */
    private $link;

    /**
     * AsiExtraField constructor.
     *
     * @param int    $mode
     * @param int    $uid
     * @param int    $gid
     * @param string $link
     */
    public function __construct($mode, $uid = self::USER_GID_PID, $gid = self::USER_GID_PID, $link = '')
    {
        $this->mode = $mode;
        $this->uid = $uid;
        $this->gid = $gid;
        $this->link = $link;
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
     * @throws Crc32Exception
     *
     * @return static
     */
    public static function unpackLocalFileData($buffer, ZipEntry $entry = null)
    {
        $givenChecksum = unpack('V', $buffer)[1];
        $buffer = substr($buffer, 4);
        $realChecksum = crc32($buffer);

        if ($givenChecksum !== $realChecksum) {
            throw new Crc32Exception('Asi Unix Extra Filed Data', $givenChecksum, $realChecksum);
        }

        $data = unpack('vmode/VlinkSize/vuid/vgid', $buffer);
        $link = '';

        if ($data['linkSize'] > 0) {
            $link = substr($buffer, 10);
        }

        return new self($data['mode'], $data['uid'], $data['gid'], $link);
    }

    /**
     * Populate data from this array as if it was in central directory data.
     *
     * @param string        $buffer the buffer to read data from
     * @param ZipEntry|null $entry
     *
     * @throws Crc32Exception
     *
     * @return AsiExtraField
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
        $data = pack(
            'vVvv',
            $this->mode,
            \strlen($this->link),
            $this->uid,
            $this->gid
        ) . $this->link;

        return pack('V', crc32($data)) . $data;
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
     * Name of linked file.
     *
     * @return string name of the file this entry links to if it is a
     *                symbolic link, the empty string otherwise
     */
    public function getLink()
    {
        return $this->link;
    }

    /**
     * Indicate that this entry is a symbolic link to the given filename.
     *
     * @param string $link name of the file this entry links to, empty
     *                     string if it is not a symbolic link
     */
    public function setLink($link)
    {
        $this->link = (string) $link;
        $this->mode = $this->getPermissionsMode($this->mode);
    }

    /**
     * Is this entry a symbolic link?
     *
     * @return bool true if this is a symbolic link
     */
    public function isLink()
    {
        return !empty($this->link);
    }

    /**
     * Get the file mode for given permissions with the correct file type.
     *
     * @param int $mode the mode
     *
     * @return int the type with the mode
     */
    protected function getPermissionsMode($mode)
    {
        $type = 0;

        if ($this->isLink()) {
            $type = UnixStat::UNX_IFLNK;
        } elseif (($mode & UnixStat::UNX_IFREG) !== 0) {
            $type = UnixStat::UNX_IFREG;
        } elseif (($mode & UnixStat::UNX_IFDIR) !== 0) {
            $type = UnixStat::UNX_IFDIR;
        }

        return $type | ($mode & self::PERM_MASK);
    }

    /**
     * Is this entry a directory?
     *
     * @return bool true if this entry is a directory
     */
    public function isDirectory()
    {
        return ($this->mode & UnixStat::UNX_IFDIR) !== 0 && !$this->isLink();
    }

    /**
     * @return int
     */
    public function getMode()
    {
        return $this->mode;
    }

    /**
     * @param int $mode
     */
    public function setMode($mode)
    {
        $this->mode = $this->getPermissionsMode($mode);
    }

    /**
     * @return int
     */
    public function getUserId()
    {
        return $this->uid;
    }

    /**
     * @param int $uid
     */
    public function setUserId($uid)
    {
        $this->uid = (int) $uid;
    }

    /**
     * @return int
     */
    public function getGroupId()
    {
        return $this->gid;
    }

    /**
     * @param int $gid
     */
    public function setGroupId($gid)
    {
        $this->gid = (int) $gid;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return sprintf(
            '0x%04x ASI: Mode=%o UID=%d GID=%d Link="%s',
            self::HEADER_ID,
            $this->mode,
            $this->uid,
            $this->gid,
            $this->link
        );
    }
}
