<?php

namespace PhpZip\Model;

/**
 * End of Central Directory.
 *
 * @author Ne-Lexa alexey@nelexa.ru
 * @license MIT
 */
class EndOfCentralDirectory
{
    /** @var int Count files. */
    private $entryCount;

    /** @var int Central Directory Offset. */
    private $cdOffset;

    /** @var int */
    private $cdSize;

    /** @var string|null The archive comment. */
    private $comment;

    /** @var bool Zip64 extension */
    private $zip64;

    /**
     * EndOfCentralDirectory constructor.
     *
     * @param int         $entryCount
     * @param int         $cdOffset
     * @param int         $cdSize
     * @param bool        $zip64
     * @param string|null $comment
     */
    public function __construct($entryCount, $cdOffset, $cdSize, $zip64, $comment = null)
    {
        $this->entryCount = $entryCount;
        $this->cdOffset = $cdOffset;
        $this->cdSize = $cdSize;
        $this->zip64 = $zip64;
        $this->comment = $comment;
    }

    /**
     * @param string|null $comment
     */
    public function setComment($comment)
    {
        $this->comment = $comment;
    }

    /**
     * @return int
     */
    public function getEntryCount()
    {
        return $this->entryCount;
    }

    /**
     * @return int
     */
    public function getCdOffset()
    {
        return $this->cdOffset;
    }

    /**
     * @return int
     */
    public function getCdSize()
    {
        return $this->cdSize;
    }

    /**
     * @return string|null
     */
    public function getComment()
    {
        return $this->comment;
    }

    /**
     * @return bool
     */
    public function isZip64()
    {
        return $this->zip64;
    }
}
