<?php

namespace PhpZip\Model;

/**
 * @author Ne-Lexa alexey@nelexa.ru
 * @license MIT
 */
class ZipEntryMatcher implements \Countable
{
    /** @var ZipContainer */
    protected $zipContainer;

    /** @var array */
    protected $matches = [];

    /**
     * ZipEntryMatcher constructor.
     *
     * @param ZipContainer $zipContainer
     */
    public function __construct(ZipContainer $zipContainer)
    {
        $this->zipContainer = $zipContainer;
    }

    /**
     * @param string|ZipEntry|string[]|ZipEntry[] $entries
     *
     * @return ZipEntryMatcher
     */
    public function add($entries)
    {
        $entries = (array) $entries;
        $entries = array_map(
            static function ($entry) {
                return $entry instanceof ZipEntry ? $entry->getName() : (string) $entry;
            },
            $entries
        );
        $this->matches = array_values(
            array_map(
                'strval',
                array_unique(
                    array_merge(
                        $this->matches,
                        array_keys(
                            array_intersect_key(
                                $this->zipContainer->getEntries(),
                                array_flip($entries)
                            )
                        )
                    )
                )
            )
        );

        return $this;
    }

    /**
     * @param string $regexp
     *
     * @return ZipEntryMatcher
     *
     * @noinspection PhpUnusedParameterInspection
     */
    public function match($regexp)
    {
        array_walk(
            $this->zipContainer->getEntries(),
            /**
             * @param ZipEntry $entry
             * @param string   $entryName
             */
            function (ZipEntry $entry, $entryName) use ($regexp) {
                if (preg_match($regexp, $entryName)) {
                    $this->matches[] = (string) $entryName;
                }
            }
        );
        $this->matches = array_unique($this->matches);

        return $this;
    }

    /**
     * @return ZipEntryMatcher
     */
    public function all()
    {
        $this->matches = array_map(
            'strval',
            array_keys($this->zipContainer->getEntries())
        );

        return $this;
    }

    /**
     * Callable function for all select entries.
     *
     * Callable function signature:
     * function(string $entryName){}
     *
     * @param callable $callable
     */
    public function invoke(callable $callable)
    {
        if (!empty($this->matches)) {
            array_walk(
                $this->matches,
                /** @param string $entryName */
                static function ($entryName) use ($callable) {
                    $callable($entryName);
                }
            );
        }
    }

    /**
     * @return array
     */
    public function getMatches()
    {
        return $this->matches;
    }

    public function delete()
    {
        array_walk(
            $this->matches,
            /** @param string $entryName */
            function ($entryName) {
                $this->zipContainer->deleteEntry($entryName);
            }
        );
        $this->matches = [];
    }

    /**
     * @param string|null $password
     * @param int|null    $encryptionMethod
     */
    public function setPassword($password, $encryptionMethod = null)
    {
        array_walk(
            $this->matches,
            /** @param string $entryName */
            function ($entryName) use ($password, $encryptionMethod) {
                $entry = $this->zipContainer->getEntry($entryName);

                if (!$entry->isDirectory()) {
                    $entry->setPassword($password, $encryptionMethod);
                }
            }
        );
    }

    /**
     * @param int $encryptionMethod
     */
    public function setEncryptionMethod($encryptionMethod)
    {
        array_walk(
            $this->matches,
            /** @param string $entryName */
            function ($entryName) use ($encryptionMethod) {
                $entry = $this->zipContainer->getEntry($entryName);

                if (!$entry->isDirectory()) {
                    $entry->setEncryptionMethod($encryptionMethod);
                }
            }
        );
    }

    public function disableEncryption()
    {
        array_walk(
            $this->matches,
            /** @param string $entryName */
            function ($entryName) {
                $entry = $this->zipContainer->getEntry($entryName);

                if (!$entry->isDirectory()) {
                    $entry->disableEncryption();
                }
            }
        );
    }

    /**
     * Count elements of an object.
     *
     * @see http://php.net/manual/en/countable.count.php
     *
     * @return int the custom count as an integer
     *
     * @since 5.1.0
     */
    public function count()
    {
        return \count($this->matches);
    }
}
