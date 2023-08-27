<?php

namespace PhpZip\Util\Iterator;

use PhpZip\Util\StringUtil;

/**
 * Recursive iterator for ignore files.
 *
 * @author Ne-Lexa alexey@nelexa.ru
 * @license MIT
 */
class IgnoreFilesRecursiveFilterIterator extends \RecursiveFilterIterator
{
    /**
     * Ignore list files.
     *
     * @var array
     */
    private $ignoreFiles = ['..'];

    /**
     * @param \RecursiveIterator $iterator
     * @param array              $ignoreFiles
     */
    public function __construct(\RecursiveIterator $iterator, array $ignoreFiles)
    {
        parent::__construct($iterator);
        $this->ignoreFiles = array_merge($this->ignoreFiles, $ignoreFiles);
    }

    /**
     * Check whether the current element of the iterator is acceptable.
     *
     * @see http://php.net/manual/en/filteriterator.accept.php
     *
     * @return bool true if the current element is acceptable, otherwise false
     *
     * @since 5.1.0
     */
    public function accept()
    {
        /**
         * @var \SplFileInfo $fileInfo
         */
        $fileInfo = $this->current();
        $pathname = str_replace('\\', '/', $fileInfo->getPathname());

        foreach ($this->ignoreFiles as $ignoreFile) {
            // handler dir and sub dir
            if ($fileInfo->isDir()
                && $ignoreFile[\strlen($ignoreFile) - 1] === '/'
                && StringUtil::endsWith($pathname, substr($ignoreFile, 0, -1))
            ) {
                return false;
            }

            // handler filename
            if (StringUtil::endsWith($pathname, $ignoreFile)) {
                return false;
            }
        }

        return true;
    }

    /**
     * @return IgnoreFilesRecursiveFilterIterator
     */
    public function getChildren()
    {
        return new self($this->getInnerIterator()->getChildren(), $this->ignoreFiles);
    }
}
