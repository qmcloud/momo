<?php

namespace PhpZip\Constants;

/**
 * Unix stat constants.
 *
 * @author Ne-Lexa alexey@nelexa.ru
 * @license MIT
 */
interface UnixStat
{
    /** @var int unix file type mask */
    const UNX_IFMT = 0170000;

    /** @var int unix regular file */
    const UNX_IFREG = 0100000;

    /** @var int unix socket (BSD, not SysV or Amiga) */
    const UNX_IFSOCK = 0140000;

    /** @var int unix symbolic link (not SysV, Amiga) */
    const UNX_IFLNK = 0120000;

    /** @var int unix block special       (not Amiga) */
    const UNX_IFBLK = 0060000;

    /** @var int unix directory */
    const UNX_IFDIR = 0040000;

    /** @var int unix character special   (not Amiga) */
    const UNX_IFCHR = 0020000;

    /** @var int unix fifo    (BCC, not MSC or Amiga) */
    const UNX_IFIFO = 0010000;

    /** @var int unix set user id on execution */
    const UNX_ISUID = 04000;

    /** @var int unix set group id on execution */
    const UNX_ISGID = 02000;

    /** @var int unix directory permissions control */
    const UNX_ISVTX = 01000;

    /** @var int unix record locking enforcement flag */
    const UNX_ENFMT = 02000;

    /** @var int unix read, write, execute: owner */
    const UNX_IRWXU = 00700;

    /** @var int unix read permission: owner */
    const UNX_IRUSR = 00400;

    /** @var int unix write permission: owner */
    const UNX_IWUSR = 00200;

    /** @var int unix execute permission: owner */
    const UNX_IXUSR = 00100;

    /** @var int unix read, write, execute: group */
    const UNX_IRWXG = 00070;

    /** @var int unix read permission: group */
    const UNX_IRGRP = 00040;

    /** @var int unix write permission: group */
    const UNX_IWGRP = 00020;

    /** @var int unix execute permission: group */
    const UNX_IXGRP = 00010;

    /** @var int unix read, write, execute: other */
    const UNX_IRWXO = 00007;

    /** @var int unix read permission: other */
    const UNX_IROTH = 00004;

    /** @var int unix write permission: other */
    const UNX_IWOTH = 00002;

    /** @var int unix execute permission: other */
    const UNX_IXOTH = 00001;
}
