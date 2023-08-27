<?php

namespace PhpZip\Model;

/**
 * Class ImmutableZipContainer.
 */
class ImmutableZipContainer implements \Countable
{
    /** @var ZipEntry[] */
    protected $entries;

    /** @var string|null Archive comment */
    protected $archiveComment;

    /**
     * ZipContainer constructor.
     *
     * @param ZipEntry[]  $entries
     * @param string|null $archiveComment
     */
    public function __construct(array $entries, $archiveComment)
    {
        $this->entries = $entries;
        $this->archiveComment = $archiveComment;
    }

    /**
     * @return ZipEntry[]
     */
    public function &getEntries()
    {
        return $this->entries;
    }

    /**
     * @return string|null
     */
    public function getArchiveComment()
    {
        return $this->archiveComment;
    }

    /**
     * Count elements of an object.
     *
     * @see https://php.net/manual/en/countable.count.php
     *
     * @return int The custom count as an integer.
     *             The return value is cast to an integer.
     */
    public function count()
    {
        return \count($this->entries);
    }

    /**
     * When an object is cloned, PHP 5 will perform a shallow copy of all of the object's properties.
     * Any properties that are references to other variables, will remain references.
     * Once the cloning is complete, if a __clone() method is defined,
     * then the newly created object's __clone() method will be called, to allow any necessary properties that need to
     * be changed. NOT CALLABLE DIRECTLY.
     *
     * @see https://php.net/manual/en/language.oop5.cloning.php
     */
    public function __clone()
    {
        foreach ($this->entries as $key => $value) {
            $this->entries[$key] = clone $value;
        }
    }
}
