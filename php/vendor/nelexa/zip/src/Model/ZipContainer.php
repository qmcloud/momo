<?php

namespace PhpZip\Model;

use PhpZip\Constants\ZipEncryptionMethod;
use PhpZip\Exception\InvalidArgumentException;
use PhpZip\Exception\ZipEntryNotFoundException;
use PhpZip\Exception\ZipException;

/**
 * Class ZipContainer.
 */
class ZipContainer extends ImmutableZipContainer
{
    /**
     * @var ImmutableZipContainer|null The source container contains zip entries from
     *                                 an open zip archive. The source container makes
     *                                 it possible to undo changes in the archive.
     *                                 When cloning, this container is not cloned.
     */
    private $sourceContainer;

    /**
     * @var int|null Apk zipalign value
     *
     * @todo remove and use in ApkFileWriter
     */
    private $zipAlign;

    /**
     * MutableZipContainer constructor.
     *
     * @param ImmutableZipContainer|null $sourceContainer
     */
    public function __construct(ImmutableZipContainer $sourceContainer = null)
    {
        $entries = [];
        $archiveComment = null;

        if ($sourceContainer !== null) {
            foreach ($sourceContainer->getEntries() as $entryName => $entry) {
                $entries[$entryName] = clone $entry;
            }
            $archiveComment = $sourceContainer->getArchiveComment();
        }
        parent::__construct($entries, $archiveComment);
        $this->sourceContainer = $sourceContainer;
    }

    /**
     * @return ImmutableZipContainer|null
     */
    public function getSourceContainer()
    {
        return $this->sourceContainer;
    }

    /**
     * @param ZipEntry $entry
     */
    public function addEntry(ZipEntry $entry)
    {
        $this->entries[$entry->getName()] = $entry;
    }

    /**
     * @param string|ZipEntry $entry
     *
     * @return bool
     */
    public function deleteEntry($entry)
    {
        $entry = $entry instanceof ZipEntry ? $entry->getName() : (string) $entry;

        if (isset($this->entries[$entry])) {
            unset($this->entries[$entry]);

            return true;
        }

        return false;
    }

    /**
     * @param string|ZipEntry $old
     * @param string|ZipEntry $new
     *
     * @throws ZipException
     *
     * @return ZipEntry New zip entry
     */
    public function renameEntry($old, $new)
    {
        $old = $old instanceof ZipEntry ? $old->getName() : (string) $old;
        $new = $new instanceof ZipEntry ? $new->getName() : (string) $new;

        if (isset($this->entries[$new])) {
            throw new InvalidArgumentException('New entry name ' . $new . ' is exists.');
        }

        $entry = $this->getEntry($old);
        $newEntry = $entry->rename($new);

        $this->deleteEntry($entry);
        $this->addEntry($newEntry);

        return $newEntry;
    }

    /**
     * @param string|ZipEntry $entryName
     *
     * @throws ZipEntryNotFoundException
     *
     * @return ZipEntry
     */
    public function getEntry($entryName)
    {
        $entry = $this->getEntryOrNull($entryName);

        if ($entry !== null) {
            return $entry;
        }

        throw new ZipEntryNotFoundException($entryName);
    }

    /**
     * @param string|ZipEntry $entryName
     *
     * @return ZipEntry|null
     */
    public function getEntryOrNull($entryName)
    {
        $entryName = $entryName instanceof ZipEntry ? $entryName->getName() : (string) $entryName;

        return isset($this->entries[$entryName]) ? $this->entries[$entryName] : null;
    }

    /**
     * @param string|ZipEntry $entryName
     *
     * @return bool
     */
    public function hasEntry($entryName)
    {
        $entryName = $entryName instanceof ZipEntry ? $entryName->getName() : (string) $entryName;

        return isset($this->entries[$entryName]);
    }

    /**
     * Delete all entries.
     */
    public function deleteAll()
    {
        $this->entries = [];
    }

    /**
     * Delete entries by regex pattern.
     *
     * @param string $regexPattern Regex pattern
     *
     * @return ZipEntry[] Deleted entries
     */
    public function deleteByRegex($regexPattern)
    {
        if (empty($regexPattern)) {
            throw new InvalidArgumentException('The regex pattern is not specified');
        }

        /** @var ZipEntry[] $found */
        $found = [];

        foreach ($this->entries as $entryName => $entry) {
            if (preg_match($regexPattern, $entryName)) {
                $found[] = $entry;
            }
        }

        foreach ($found as $entry) {
            $this->deleteEntry($entry);
        }

        return $found;
    }

    /**
     * Undo all changes done in the archive.
     */
    public function unchangeAll()
    {
        $this->entries = [];

        if ($this->sourceContainer !== null) {
            foreach ($this->sourceContainer->getEntries() as $entry) {
                $this->entries[$entry->getName()] = clone $entry;
            }
        }
        $this->unchangeArchiveComment();
    }

    /**
     * Undo change archive comment.
     */
    public function unchangeArchiveComment()
    {
        $this->archiveComment = null;

        if ($this->sourceContainer !== null) {
            $this->archiveComment = $this->sourceContainer->archiveComment;
        }
    }

    /**
     * Revert all changes done to an entry with the given name.
     *
     * @param string|ZipEntry $entry Entry name or ZipEntry
     *
     * @return bool
     */
    public function unchangeEntry($entry)
    {
        $entry = $entry instanceof ZipEntry ? $entry->getName() : (string) $entry;

        if (
            $this->sourceContainer !== null &&
            isset($this->entries[$entry], $this->sourceContainer->entries[$entry])
        ) {
            $this->entries[$entry] = clone $this->sourceContainer->entries[$entry];

            return true;
        }

        return false;
    }

    /**
     * Entries sort by name.
     *
     * Example:
     * ```php
     * $zipContainer->sortByName(static function (string $nameA, string $nameB): int {
     *     return strcmp($nameA, $nameB);
     * });
     * ```
     *
     * @param callable $cmp
     */
    public function sortByName(callable $cmp)
    {
        uksort($this->entries, $cmp);
    }

    /**
     * Entries sort by entry.
     *
     * Example:
     * ```php
     * $zipContainer->sortByEntry(static function (ZipEntry $a, ZipEntry $b): int {
     *     return strcmp($a->getName(), $b->getName());
     * });
     * ```
     *
     * @param callable $cmp
     */
    public function sortByEntry(callable $cmp)
    {
        uasort($this->entries, $cmp);
    }

    /**
     * @param string|null $archiveComment
     */
    public function setArchiveComment($archiveComment)
    {
        if ($archiveComment !== null && $archiveComment !== '') {
            $archiveComment = (string) $archiveComment;
            $length = \strlen($archiveComment);

            if ($length > 0xffff) {
                throw new InvalidArgumentException('Length comment out of range');
            }
        }
        $this->archiveComment = $archiveComment;
    }

    /**
     * @return ZipEntryMatcher
     */
    public function matcher()
    {
        return new ZipEntryMatcher($this);
    }

    /**
     * Specify a password for extracting files.
     *
     * @param string|null $password
     */
    public function setReadPassword($password)
    {
        if ($this->sourceContainer !== null) {
            foreach ($this->sourceContainer->entries as $entry) {
                if ($entry->isEncrypted()) {
                    $entry->setPassword($password);
                }
            }
        }
    }

    /**
     * @param string $entryName
     * @param string $password
     *
     * @throws ZipEntryNotFoundException
     * @throws ZipException
     */
    public function setReadPasswordEntry($entryName, $password)
    {
        if (!isset($this->sourceContainer->entries[$entryName])) {
            throw new ZipEntryNotFoundException($entryName);
        }

        if ($this->sourceContainer->entries[$entryName]->isEncrypted()) {
            $this->sourceContainer->entries[$entryName]->setPassword($password);
        }
    }

    /**
     * @return int|null
     */
    public function getZipAlign()
    {
        return $this->zipAlign;
    }

    /**
     * @param int|null $zipAlign
     */
    public function setZipAlign($zipAlign)
    {
        $this->zipAlign = $zipAlign === null ? null : (int) $zipAlign;
    }

    /**
     * @return bool
     */
    public function isZipAlign()
    {
        return $this->zipAlign !== null;
    }

    /**
     * @param string|null $writePassword
     */
    public function setWritePassword($writePassword)
    {
        $this->matcher()->all()->setPassword($writePassword);
    }

    /**
     * Remove password.
     */
    public function removePassword()
    {
        $this->matcher()->all()->setPassword(null);
    }

    /**
     * @param string|ZipEntry $entryName
     */
    public function removePasswordEntry($entryName)
    {
        $this->matcher()->add($entryName)->setPassword(null);
    }

    /**
     * @param int $encryptionMethod
     */
    public function setEncryptionMethod($encryptionMethod = ZipEncryptionMethod::WINZIP_AES_256)
    {
        $this->matcher()->all()->setEncryptionMethod($encryptionMethod);
    }
}
