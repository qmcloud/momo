<?php

namespace PhpZip\Constants;

/**
 * Interface DosAttrs.
 */
interface DosAttrs
{
    /** @var int DOS File Attribute Read Only */
    const DOS_READ_ONLY = 0x01;

    /** @var int DOS File Attribute Hidden */
    const DOS_HIDDEN = 0x02;

    /** @var int DOS File Attribute System */
    const DOS_SYSTEM = 0x04;

    /** @var int DOS File Attribute Label */
    const DOS_LABEL = 0x08;

    /** @var int DOS File Attribute Directory */
    const DOS_DIRECTORY = 0x10;

    /** @var int DOS File Attribute Archive */
    const DOS_ARCHIVE = 0x20;

    /** @var int DOS File Attribute Link */
    const DOS_LINK = 0x40;

    /** @var int DOS File Attribute Execute */
    const DOS_EXE = 0x80;
}
