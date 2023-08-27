<?php

namespace PhpZip\Util\Iterator;

use PhpZip\Util\StringUtil;

/**
 * Iterator for ignore files.
 *
 * @author Ne-Lexa alexey@nelexa.ru
 * @license MIT
 */
class IgnoreFilesFilterIterator extends \FilterIterator
{
    /**
     * Ignore list files.
     *
     * @var array
     */
    private $ignoreFiles = ['..'];

    /**
     * @param \Iterator $iterator
     * @param array     $ignoreFiles
     */
    public function __construct(\Iterator $iterator, array $ignoreFiles)
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
                && StringUtil::endsWith($ignoreFile, '/')
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
}
