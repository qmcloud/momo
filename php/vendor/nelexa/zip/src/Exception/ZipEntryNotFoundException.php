<?php

namespace PhpZip\Exception;

use PhpZip\Model\ZipEntry;

/**
 * Thrown if entry not found.
 *
 * @author Ne-Lexa alexey@nelexa.ru
 * @license MIT
 */
class ZipEntryNotFoundException extends ZipException
{
    /** @var string */
    private $entryName;

    /**
     * ZipEntryNotFoundException constructor.
     *
     * @param ZipEntry|string $entryName
     */
    public function __construct($entryName)
    {
        $entryName = $entryName instanceof ZipEntry ? $entryName->getName() : $entryName;
        parent::__construct(sprintf(
            'Zip Entry "%s" was not found in the archive.',
            $entryName
        ));
        $this->entryName = $entryName;
    }

    /**
     * @return string
     */
    public function getEntryName()
    {
        return $this->entryName;
    }
}
