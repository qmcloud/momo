<?php

namespace PhpZip;

use PhpZip\Constants\UnixStat;
use PhpZip\Constants\ZipCompressionLevel;
use PhpZip\Constants\ZipCompressionMethod;
use PhpZip\Constants\ZipEncryptionMethod;
use PhpZip\Constants\ZipOptions;
use PhpZip\Constants\ZipPlatform;
use PhpZip\Exception\InvalidArgumentException;
use PhpZip\Exception\ZipEntryNotFoundException;
use PhpZip\Exception\ZipException;
use PhpZip\IO\Stream\ResponseStream;
use PhpZip\IO\Stream\ZipEntryStreamWrapper;
use PhpZip\IO\ZipReader;
use PhpZip\IO\ZipWriter;
use PhpZip\Model\Data\ZipFileData;
use PhpZip\Model\Data\ZipNewData;
use PhpZip\Model\ImmutableZipContainer;
use PhpZip\Model\ZipContainer;
use PhpZip\Model\ZipEntry;
use PhpZip\Model\ZipEntryMatcher;
use PhpZip\Model\ZipInfo;
use PhpZip\Util\FilesUtil;
use PhpZip\Util\StringUtil;
use Psr\Http\Message\ResponseInterface;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo as SymfonySplFileInfo;

/**
 * Create, open .ZIP files, modify, get info and extract files.
 *
 * Implemented support traditional PKWARE encryption and WinZip AES encryption.
 * Implemented support ZIP64.
 * Support ZipAlign functional.
 *
 * @see https://pkware.cachefly.net/webdocs/casestudies/APPNOTE.TXT .ZIP File Format Specification
 *
 * @author Ne-Lexa alexey@nelexa.ru
 * @license MIT
 */
class ZipFile implements ZipFileInterface
{
    /** @var array default mime types */
    private static $defaultMimeTypes = [
        'zip' => 'application/zip',
        'apk' => 'application/vnd.android.package-archive',
        'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        'epub' => 'application/epub+zip',
        'jar' => 'application/java-archive',
        'odt' => 'application/vnd.oasis.opendocument.text',
        'pptx' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.template',
        'xlsx' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        'xpi' => 'application/x-xpinstall',
    ];

    /** @var ZipContainer */
    protected $zipContainer;

    /** @var ZipReader|null */
    private $reader;

    /**
     * ZipFile constructor.
     */
    public function __construct()
    {
        $this->zipContainer = $this->createZipContainer(null);
    }

    /**
     * @param resource $inputStream
     * @param array    $options
     *
     * @return ZipReader
     */
    protected function createZipReader($inputStream, array $options = [])
    {
        return new ZipReader($inputStream, $options);
    }

    /**
     * @return ZipWriter
     */
    protected function createZipWriter()
    {
        return new ZipWriter($this->zipContainer);
    }

    /**
     * @param ImmutableZipContainer|null $sourceContainer
     *
     * @return ZipContainer
     */
    protected function createZipContainer(ImmutableZipContainer $sourceContainer = null)
    {
        return new ZipContainer($sourceContainer);
    }

    /**
     * Open zip archive from file.
     *
     * @param string $filename
     * @param array  $options
     *
     * @throws ZipException if can't open file
     *
     * @return ZipFile
     */
    public function openFile($filename, array $options = [])
    {
        if (!file_exists($filename)) {
            throw new ZipException("File {$filename} does not exist.");
        }

        if (!($handle = @fopen($filename, 'rb'))) {
            throw new ZipException("File {$filename} can't open.");
        }

        return $this->openFromStream($handle, $options);
    }

    /**
     * Open zip archive from raw string data.
     *
     * @param string $data
     * @param array  $options
     *
     * @throws ZipException if can't open temp stream
     *
     * @return ZipFile
     */
    public function openFromString($data, array $options = [])
    {
        if ($data === null || $data === '') {
            throw new InvalidArgumentException('Empty string passed');
        }

        if (!($handle = fopen('php://temp', 'r+b'))) {
            // @codeCoverageIgnoreStart
            throw new ZipException('A temporary resource cannot be opened for writing.');
            // @codeCoverageIgnoreEnd
        }
        fwrite($handle, $data);
        rewind($handle);

        return $this->openFromStream($handle, $options);
    }

    /**
     * Open zip archive from stream resource.
     *
     * @param resource $handle
     * @param array    $options
     *
     * @throws ZipException
     *
     * @return ZipFile
     */
    public function openFromStream($handle, array $options = [])
    {
        $this->reader = $this->createZipReader($handle, $options);
        $this->zipContainer = $this->createZipContainer($this->reader->read());

        return $this;
    }

    /**
     * @return string[] returns the list files
     */
    public function getListFiles()
    {
        // strval is needed to cast entry names to string type
        return array_map('strval', array_keys($this->zipContainer->getEntries()));
    }

    /**
     * @return int returns the number of entries in this ZIP file
     */
    public function count()
    {
        return $this->zipContainer->count();
    }

    /**
     * Returns the file comment.
     *
     * @return string|null the file comment
     */
    public function getArchiveComment()
    {
        return $this->zipContainer->getArchiveComment();
    }

    /**
     * Set archive comment.
     *
     * @param string|null $comment
     *
     * @return ZipFile
     */
    public function setArchiveComment($comment = null)
    {
        $this->zipContainer->setArchiveComment($comment);

        return $this;
    }

    /**
     * Checks if there is an entry in the archive.
     *
     * @param string $entryName
     *
     * @return bool
     */
    public function hasEntry($entryName)
    {
        return $this->zipContainer->hasEntry($entryName);
    }

    /**
     * Returns ZipEntry object.
     *
     * @param string $entryName
     *
     * @throws ZipEntryNotFoundException
     *
     * @return ZipEntry
     */
    public function getEntry($entryName)
    {
        return $this->zipContainer->getEntry($entryName);
    }

    /**
     * Checks that the entry in the archive is a directory.
     * Returns true if and only if this ZIP entry represents a directory entry
     * (i.e. end with '/').
     *
     * @param string $entryName
     *
     * @throws ZipEntryNotFoundException
     *
     * @return bool
     */
    public function isDirectory($entryName)
    {
        return $this->getEntry($entryName)->isDirectory();
    }

    /**
     * Returns entry comment.
     *
     * @param string $entryName
     *
     * @throws ZipEntryNotFoundException
     * @throws ZipException
     *
     * @return string
     */
    public function getEntryComment($entryName)
    {
        return $this->getEntry($entryName)->getComment();
    }

    /**
     * Set entry comment.
     *
     * @param string      $entryName
     * @param string|null $comment
     *
     * @throws ZipException
     * @throws ZipEntryNotFoundException
     *
     * @return ZipFile
     */
    public function setEntryComment($entryName, $comment = null)
    {
        $this->getEntry($entryName)->setComment($comment);

        return $this;
    }

    /**
     * Returns the entry contents.
     *
     * @param string $entryName
     *
     * @throws ZipException
     * @throws ZipEntryNotFoundException
     *
     * @return string
     */
    public function getEntryContents($entryName)
    {
        $zipData = $this->zipContainer->getEntry($entryName)->getData();

        if ($zipData === null) {
            throw new ZipException(sprintf('No data for zip entry %s', $entryName));
        }

        return $zipData->getDataAsString();
    }

    /**
     * @param string $entryName
     *
     * @throws ZipException
     * @throws ZipEntryNotFoundException
     *
     * @return resource
     */
    public function getEntryStream($entryName)
    {
        $resource = ZipEntryStreamWrapper::wrap($this->zipContainer->getEntry($entryName));
        rewind($resource);

        return $resource;
    }

    /**
     * Get info by entry.
     *
     * @param string|ZipEntry $entryName
     *
     * @throws ZipEntryNotFoundException
     * @throws ZipException
     *
     * @return ZipInfo
     */
    public function getEntryInfo($entryName)
    {
        return new ZipInfo($this->zipContainer->getEntry($entryName));
    }

    /**
     * Get info by all entries.
     *
     * @return ZipInfo[]
     */
    public function getAllInfo()
    {
        $infoMap = [];

        foreach ($this->zipContainer->getEntries() as $name => $entry) {
            $infoMap[$name] = new ZipInfo($entry);
        }

        return $infoMap;
    }

    /**
     * @return ZipEntryMatcher
     */
    public function matcher()
    {
        return $this->zipContainer->matcher();
    }

    /**
     * Returns an array of zip records (ex. for modify time).
     *
     * @return ZipEntry[] array of raw zip entries
     */
    public function getEntries()
    {
        return $this->zipContainer->getEntries();
    }

    /**
     * Extract the archive contents (unzip).
     *
     * Extract the complete archive or the given files to the specified destination.
     *
     * @param string            $destDir          location where to extract the files
     * @param array|string|null $entries          entries to extract
     * @param array             $options          extract options
     * @param array             $extractedEntries if the extractedEntries argument
     *                                            is present, then the  specified
     *                                            array will be filled with
     *                                            information about the
     *                                            extracted entries
     *
     * @throws ZipException
     *
     * @return ZipFile
     */
    public function extractTo($destDir, $entries = null, array $options = [], &$extractedEntries = [])
    {
        if (!file_exists($destDir)) {
            throw new ZipException(sprintf('Destination %s not found', $destDir));
        }

        if (!is_dir($destDir)) {
            throw new ZipException('Destination is not directory');
        }

        if (!is_writable($destDir)) {
            throw new ZipException('Destination is not writable directory');
        }

        if ($extractedEntries === null) {
            $extractedEntries = [];
        }

        $defaultOptions = [
            ZipOptions::EXTRACT_SYMLINKS => false,
        ];
        /** @noinspection AdditionOperationOnArraysInspection */
        $options += $defaultOptions;

        $zipEntries = $this->zipContainer->getEntries();

        if (!empty($entries)) {
            if (\is_string($entries)) {
                $entries = (array) $entries;
            }

            if (\is_array($entries)) {
                $entries = array_unique($entries);
                $zipEntries = array_intersect_key($zipEntries, array_flip($entries));
            }
        }

        if (empty($zipEntries)) {
            return $this;
        }

        /** @var int[] $lastModDirs */
        $lastModDirs = [];

        krsort($zipEntries, \SORT_NATURAL);

        $symlinks = [];
        $destDir = rtrim($destDir, '/\\');

        foreach ($zipEntries as $entryName => $entry) {
            $unixMode = $entry->getUnixMode();
            $entryName = FilesUtil::normalizeZipPath($entryName);
            $file = $destDir . \DIRECTORY_SEPARATOR . $entryName;

            $extractedEntries[$file] = $entry;
            $modifyTimestamp = $entry->getMTime()->getTimestamp();
            $atime = $entry->getATime();
            $accessTimestamp = $atime === null ? null : $atime->getTimestamp();

            $dir = $entry->isDirectory() ? $file : \dirname($file);

            if (!is_dir($dir)) {
                $dirMode = $entry->isDirectory() ? $unixMode : 0755;

                if ($dirMode === 0) {
                    $dirMode = 0755;
                }

                if (!mkdir($dir, $dirMode, true) && !is_dir($dir)) {
                    // @codeCoverageIgnoreStart
                    throw new \RuntimeException(sprintf('Directory "%s" was not created', $dir));
                    // @codeCoverageIgnoreEnd
                }
                chmod($dir, $dirMode);
            }

            $parts = explode('/', rtrim($entryName, '/'));
            $path = $destDir . \DIRECTORY_SEPARATOR;

            foreach ($parts as $part) {
                if (!isset($lastModDirs[$path]) || $lastModDirs[$path] > $modifyTimestamp) {
                    $lastModDirs[$path] = $modifyTimestamp;
                }

                $path .= $part . \DIRECTORY_SEPARATOR;
            }

            if ($entry->isDirectory()) {
                $lastModDirs[$dir] = $modifyTimestamp;

                continue;
            }

            $zipData = $entry->getData();

            if ($zipData === null) {
                continue;
            }

            if ($entry->isUnixSymlink()) {
                $symlinks[$file] = $zipData->getDataAsString();

                continue;
            }

            /** @noinspection PhpUsageOfSilenceOperatorInspection */
            if (!($handle = @fopen($file, 'w+b'))) {
                // @codeCoverageIgnoreStart
                throw new ZipException(
                    sprintf(
                        'Cannot extract zip entry %s. File %s cannot open for write.',
                        $entry->getName(),
                        $file
                    )
                );
                // @codeCoverageIgnoreEnd
            }

            try {
                $zipData->copyDataToStream($handle);
            } catch (ZipException $e) {
                unlink($file);

                throw $e;
            }
            fclose($handle);

            if ($unixMode === 0) {
                $unixMode = 0644;
            }
            chmod($file, $unixMode);

            if ($accessTimestamp !== null) {
                /** @noinspection PotentialMalwareInspection */
                touch($file, $modifyTimestamp, $accessTimestamp);
            } else {
                touch($file, $modifyTimestamp);
            }
        }

        $allowSymlink = (bool) $options[ZipOptions::EXTRACT_SYMLINKS];

        foreach ($symlinks as $linkPath => $target) {
            if (!FilesUtil::symlink($target, $linkPath, $allowSymlink)) {
                unset($extractedEntries[$linkPath]);
            }
        }

        krsort($lastModDirs, \SORT_NATURAL);

        foreach ($lastModDirs as $dir => $lastMod) {
            touch($dir, $lastMod);
        }

        ksort($extractedEntries);

        return $this;
    }

    /**
     * Add entry from the string.
     *
     * @param string   $entryName         zip entry name
     * @param string   $contents          string contents
     * @param int|null $compressionMethod Compression method.
     *                                    Use {@see ZipCompressionMethod::STORED},
     *                                    {@see ZipCompressionMethod::DEFLATED} or
     *                                    {@see ZipCompressionMethod::BZIP2}.
     *                                    If null, then auto choosing method.
     *
     * @throws ZipException
     *
     * @return ZipFile
     */
    public function addFromString($entryName, $contents, $compressionMethod = null)
    {
        $entryName = $this->normalizeEntryName($entryName);

        if ($contents === null) {
            throw new InvalidArgumentException('Contents is null');
        }

        $contents = (string) $contents;
        $length = \strlen($contents);

        if ($compressionMethod === null || $compressionMethod === ZipEntry::UNKNOWN) {
            if ($length < 512) {
                $compressionMethod = ZipCompressionMethod::STORED;
            } else {
                $mimeType = FilesUtil::getMimeTypeFromString($contents);
                $compressionMethod = FilesUtil::isBadCompressionMimeType($mimeType) ?
                    ZipCompressionMethod::STORED :
                    ZipCompressionMethod::DEFLATED;
            }
        }

        $zipEntry = new ZipEntry($entryName);
        $zipEntry->setData(new ZipNewData($zipEntry, $contents));
        $zipEntry->setUncompressedSize($length);
        $zipEntry->setCompressionMethod($compressionMethod);
        $zipEntry->setCreatedOS(ZipPlatform::OS_UNIX);
        $zipEntry->setExtractedOS(ZipPlatform::OS_UNIX);
        $zipEntry->setUnixMode(0100644);
        $zipEntry->setTime(time());

        $this->addZipEntry($zipEntry);

        return $this;
    }

    /**
     * @param string $entryName
     *
     * @return string
     */
    protected function normalizeEntryName($entryName)
    {
        if ($entryName === null) {
            throw new InvalidArgumentException('Entry name is null');
        }

        $entryName = ltrim((string) $entryName, '\\/');

        if (\DIRECTORY_SEPARATOR === '\\') {
            $entryName = str_replace('\\', '/', $entryName);
        }

        if ($entryName === '') {
            throw new InvalidArgumentException('Empty entry name');
        }

        return $entryName;
    }

    /**
     * @param Finder $finder
     * @param array  $options
     *
     * @throws ZipException
     *
     * @return ZipEntry[]
     */
    public function addFromFinder(Finder $finder, array $options = [])
    {
        $defaultOptions = [
            ZipOptions::STORE_ONLY_FILES => false,
            ZipOptions::COMPRESSION_METHOD => null,
            ZipOptions::MODIFIED_TIME => null,
        ];
        /** @noinspection AdditionOperationOnArraysInspection */
        $options += $defaultOptions;

        if ($options[ZipOptions::STORE_ONLY_FILES]) {
            $finder->files();
        }

        $entries = [];

        foreach ($finder as $fileInfo) {
            if ($fileInfo->isReadable()) {
                $entry = $this->addSplFile($fileInfo, null, $options);
                $entries[$entry->getName()] = $entry;
            }
        }

        return $entries;
    }

    /**
     * @param \SplFileInfo $file
     * @param string|null  $entryName
     * @param array        $options
     *
     * @throws ZipException
     *
     * @return ZipEntry
     */
    public function addSplFile(\SplFileInfo $file, $entryName = null, array $options = [])
    {
        if ($file instanceof \DirectoryIterator) {
            throw new InvalidArgumentException('File should not be \DirectoryIterator.');
        }
        $defaultOptions = [
            ZipOptions::COMPRESSION_METHOD => null,
            ZipOptions::MODIFIED_TIME => null,
        ];
        /** @noinspection AdditionOperationOnArraysInspection */
        $options += $defaultOptions;

        if (!$file->isReadable()) {
            throw new InvalidArgumentException(sprintf('File %s is not readable', $file->getPathname()));
        }

        if ($entryName === null) {
            if ($file instanceof SymfonySplFileInfo) {
                $entryName = $file->getRelativePathname();
            } else {
                $entryName = $file->getBasename();
            }
        }

        $entryName = $this->normalizeEntryName($entryName);
        $entryName = $file->isDir() ? rtrim($entryName, '/\\') . '/' : $entryName;

        $zipEntry = new ZipEntry($entryName);
        $zipEntry->setCreatedOS(ZipPlatform::OS_UNIX);
        $zipEntry->setExtractedOS(ZipPlatform::OS_UNIX);

        $zipData = null;
        $filePerms = $file->getPerms();

        if ($file->isLink()) {
            $linkTarget = $file->getLinkTarget();
            $lengthLinkTarget = \strlen($linkTarget);

            $zipEntry->setCompressionMethod(ZipCompressionMethod::STORED);
            $zipEntry->setUncompressedSize($lengthLinkTarget);
            $zipEntry->setCompressedSize($lengthLinkTarget);
            $zipEntry->setCrc(crc32($linkTarget));
            $filePerms |= UnixStat::UNX_IFLNK;

            $zipData = new ZipNewData($zipEntry, $linkTarget);
        } elseif ($file->isFile()) {
            if (isset($options[ZipOptions::COMPRESSION_METHOD])) {
                $compressionMethod = $options[ZipOptions::COMPRESSION_METHOD];
            } elseif ($file->getSize() < 512) {
                $compressionMethod = ZipCompressionMethod::STORED;
            } else {
                $compressionMethod = FilesUtil::isBadCompressionFile($file->getPathname()) ?
                    ZipCompressionMethod::STORED :
                    ZipCompressionMethod::DEFLATED;
            }

            $zipEntry->setCompressionMethod($compressionMethod);

            $zipData = new ZipFileData($zipEntry, $file);
        } elseif ($file->isDir()) {
            $zipEntry->setCompressionMethod(ZipCompressionMethod::STORED);
            $zipEntry->setUncompressedSize(0);
            $zipEntry->setCompressedSize(0);
            $zipEntry->setCrc(0);
        }

        $zipEntry->setUnixMode($filePerms);

        $timestamp = null;

        if (isset($options[ZipOptions::MODIFIED_TIME])) {
            $mtime = $options[ZipOptions::MODIFIED_TIME];

            if ($mtime instanceof \DateTimeInterface) {
                $timestamp = $mtime->getTimestamp();
            } elseif (is_numeric($mtime)) {
                $timestamp = (int) $mtime;
            } elseif (\is_string($mtime)) {
                $timestamp = strtotime($mtime);

                if ($timestamp === false) {
                    $timestamp = null;
                }
            }
        }

        if ($timestamp === null) {
            $timestamp = $file->getMTime();
        }

        $zipEntry->setTime($timestamp);
        $zipEntry->setData($zipData);

        $this->addZipEntry($zipEntry);

        return $zipEntry;
    }

    /**
     * @param ZipEntry $zipEntry
     */
    protected function addZipEntry(ZipEntry $zipEntry)
    {
        $this->zipContainer->addEntry($zipEntry);
    }

    /**
     * Add entry from the file.
     *
     * @param string      $filename          destination file
     * @param string|null $entryName         zip Entry name
     * @param int|null    $compressionMethod Compression method.
     *                                       Use {@see ZipCompressionMethod::STORED},
     *                                       {@see ZipCompressionMethod::DEFLATED} or
     *                                       {@see ZipCompressionMethod::BZIP2}.
     *                                       If null, then auto choosing method.
     *
     * @throws ZipException
     *
     * @return ZipFile
     */
    public function addFile($filename, $entryName = null, $compressionMethod = null)
    {
        if ($filename === null) {
            throw new InvalidArgumentException('Filename is null');
        }

        $this->addSplFile(
            new \SplFileInfo($filename),
            $entryName,
            [
                ZipOptions::COMPRESSION_METHOD => $compressionMethod,
            ]
        );

        return $this;
    }

    /**
     * Add entry from the stream.
     *
     * @param resource $stream            stream resource
     * @param string   $entryName         zip Entry name
     * @param int|null $compressionMethod Compression method.
     *                                    Use {@see ZipCompressionMethod::STORED},
     *                                    {@see ZipCompressionMethod::DEFLATED} or
     *                                    {@see ZipCompressionMethod::BZIP2}.
     *                                    If null, then auto choosing method.
     *
     * @throws ZipException
     *
     * @return ZipFile
     */
    public function addFromStream($stream, $entryName, $compressionMethod = null)
    {
        if (!\is_resource($stream)) {
            throw new InvalidArgumentException('Stream is not resource');
        }

        $entryName = $this->normalizeEntryName($entryName);
        $zipEntry = new ZipEntry($entryName);
        $fstat = fstat($stream);

        if ($fstat !== false) {
            $unixMode = $fstat['mode'];
            $length = $fstat['size'];

            if ($compressionMethod === null || $compressionMethod === ZipEntry::UNKNOWN) {
                if ($length < 512) {
                    $compressionMethod = ZipCompressionMethod::STORED;
                } else {
                    rewind($stream);
                    $bufferContents = stream_get_contents($stream, min(1024, $length));
                    rewind($stream);
                    $mimeType = FilesUtil::getMimeTypeFromString($bufferContents);
                    $compressionMethod = FilesUtil::isBadCompressionMimeType($mimeType) ?
                        ZipCompressionMethod::STORED :
                        ZipCompressionMethod::DEFLATED;
                }
                $zipEntry->setUncompressedSize($length);
            }
        } else {
            $unixMode = 0100644;

            if ($compressionMethod === null || $compressionMethod === ZipEntry::UNKNOWN) {
                $compressionMethod = ZipCompressionMethod::DEFLATED;
            }
        }

        $zipEntry->setCreatedOS(ZipPlatform::OS_UNIX);
        $zipEntry->setExtractedOS(ZipPlatform::OS_UNIX);
        $zipEntry->setUnixMode($unixMode);
        $zipEntry->setCompressionMethod($compressionMethod);
        $zipEntry->setTime(time());
        $zipEntry->setData(new ZipNewData($zipEntry, $stream));

        $this->addZipEntry($zipEntry);

        return $this;
    }

    /**
     * Add an empty directory in the zip archive.
     *
     * @param string $dirName
     *
     * @throws ZipException
     *
     * @return ZipFile
     */
    public function addEmptyDir($dirName)
    {
        $dirName = $this->normalizeEntryName($dirName);
        $dirName = rtrim($dirName, '\\/') . '/';

        $zipEntry = new ZipEntry($dirName);
        $zipEntry->setCompressionMethod(ZipCompressionMethod::STORED);
        $zipEntry->setUncompressedSize(0);
        $zipEntry->setCompressedSize(0);
        $zipEntry->setCrc(0);
        $zipEntry->setCreatedOS(ZipPlatform::OS_UNIX);
        $zipEntry->setExtractedOS(ZipPlatform::OS_UNIX);
        $zipEntry->setUnixMode(040755);
        $zipEntry->setTime(time());

        $this->addZipEntry($zipEntry);

        return $this;
    }

    /**
     * Add directory not recursively to the zip archive.
     *
     * @param string   $inputDir          Input directory
     * @param string   $localPath         add files to this directory, or the root
     * @param int|null $compressionMethod Compression method.
     *
     *                                    Use {@see ZipCompressionMethod::STORED}, {@see
     *     ZipCompressionMethod::DEFLATED} or
     *                                    {@see ZipCompressionMethod::BZIP2}. If null, then auto choosing method.
     *
     * @throws ZipException
     *
     * @return ZipFile
     */
    public function addDir($inputDir, $localPath = '/', $compressionMethod = null)
    {
        if ($inputDir === null) {
            throw new InvalidArgumentException('Input dir is null');
        }
        $inputDir = (string) $inputDir;

        if ($inputDir === '') {
            throw new InvalidArgumentException('The input directory is not specified');
        }

        if (!is_dir($inputDir)) {
            throw new InvalidArgumentException(sprintf('The "%s" directory does not exist.', $inputDir));
        }
        $inputDir = rtrim($inputDir, '/\\') . \DIRECTORY_SEPARATOR;

        $directoryIterator = new \DirectoryIterator($inputDir);

        return $this->addFilesFromIterator($directoryIterator, $localPath, $compressionMethod);
    }

    /**
     * Add recursive directory to the zip archive.
     *
     * @param string   $inputDir          Input directory
     * @param string   $localPath         add files to this directory, or the root
     * @param int|null $compressionMethod Compression method.
     *                                    Use {@see ZipCompressionMethod::STORED}, {@see
     *                                    ZipCompressionMethod::DEFLATED} or
     *                                    {@see ZipCompressionMethod::BZIP2}. If null, then auto choosing method.
     *
     * @throws ZipException
     *
     * @return ZipFile
     *
     * @see ZipCompressionMethod::STORED
     * @see ZipCompressionMethod::DEFLATED
     * @see ZipCompressionMethod::BZIP2
     */
    public function addDirRecursive($inputDir, $localPath = '/', $compressionMethod = null)
    {
        if ($inputDir === null) {
            throw new InvalidArgumentException('Input dir is null');
        }
        $inputDir = (string) $inputDir;

        if ($inputDir === '') {
            throw new InvalidArgumentException('The input directory is not specified');
        }

        if (!is_dir($inputDir)) {
            throw new InvalidArgumentException(sprintf('The "%s" directory does not exist.', $inputDir));
        }
        $inputDir = rtrim($inputDir, '/\\') . \DIRECTORY_SEPARATOR;

        $directoryIterator = new \RecursiveDirectoryIterator($inputDir);

        return $this->addFilesFromIterator($directoryIterator, $localPath, $compressionMethod);
    }

    /**
     * Add directories from directory iterator.
     *
     * @param \Iterator $iterator          directory iterator
     * @param string    $localPath         add files to this directory, or the root
     * @param int|null  $compressionMethod Compression method.
     *                                     Use {@see ZipCompressionMethod::STORED}, {@see
     *                                     ZipCompressionMethod::DEFLATED} or
     *                                     {@see ZipCompressionMethod::BZIP2}. If null, then auto choosing method.
     *
     * @throws ZipException
     *
     * @return ZipFile
     *
     * @see ZipCompressionMethod::STORED
     * @see ZipCompressionMethod::DEFLATED
     * @see ZipCompressionMethod::BZIP2
     */
    public function addFilesFromIterator(
        \Iterator $iterator,
        $localPath = '/',
        $compressionMethod = null
    ) {
        $localPath = (string) $localPath;

        if ($localPath !== '') {
            $localPath = trim($localPath, '\\/');
        } else {
            $localPath = '';
        }

        $iterator = $iterator instanceof \RecursiveIterator ?
            new \RecursiveIteratorIterator($iterator) :
            new \IteratorIterator($iterator);
        /**
         * @var string[] $files
         * @var string   $path
         */
        $files = [];

        foreach ($iterator as $file) {
            if ($file instanceof \SplFileInfo) {
                if ($file->getBasename() === '..') {
                    continue;
                }

                if ($file->getBasename() === '.') {
                    $files[] = \dirname($file->getPathname());
                } else {
                    $files[] = $file->getPathname();
                }
            }
        }

        if (empty($files)) {
            return $this;
        }

        natcasesort($files);
        $path = array_shift($files);

        $this->doAddFiles($path, $files, $localPath, $compressionMethod);

        return $this;
    }

    /**
     * Add files from glob pattern.
     *
     * @param string   $inputDir          Input directory
     * @param string   $globPattern       glob pattern
     * @param string   $localPath         add files to this directory, or the root
     * @param int|null $compressionMethod Compression method.
     *                                    Use {@see ZipCompressionMethod::STORED},
     *                                    {@see ZipCompressionMethod::DEFLATED} or
     *                                    {@see ZipCompressionMethod::BZIP2}. If null, then auto choosing method.
     *
     * @throws ZipException
     *
     * @return ZipFile
     * @sse https://en.wikipedia.org/wiki/Glob_(programming) Glob pattern syntax
     */
    public function addFilesFromGlob($inputDir, $globPattern, $localPath = '/', $compressionMethod = null)
    {
        return $this->addGlob($inputDir, $globPattern, $localPath, false, $compressionMethod);
    }

    /**
     * Add files from glob pattern.
     *
     * @param string   $inputDir          Input directory
     * @param string   $globPattern       glob pattern
     * @param string   $localPath         add files to this directory, or the root
     * @param bool     $recursive         recursive search
     * @param int|null $compressionMethod Compression method.
     *                                    Use {@see ZipCompressionMethod::STORED},
     *                                    {@see ZipCompressionMethod::DEFLATED} or
     *                                    {@see ZipCompressionMethod::BZIP2}. If null, then auto choosing method.
     *
     * @throws ZipException
     *
     * @return ZipFile
     *
     * @sse https://en.wikipedia.org/wiki/Glob_(programming) Glob pattern syntax
     */
    private function addGlob(
        $inputDir,
        $globPattern,
        $localPath = '/',
        $recursive = true,
        $compressionMethod = null
    ) {
        if ($inputDir === null) {
            throw new InvalidArgumentException('Input dir is null');
        }
        $inputDir = (string) $inputDir;

        if ($inputDir === '') {
            throw new InvalidArgumentException('The input directory is not specified');
        }

        if (!is_dir($inputDir)) {
            throw new InvalidArgumentException(sprintf('The "%s" directory does not exist.', $inputDir));
        }
        $globPattern = (string) $globPattern;

        if (empty($globPattern)) {
            throw new InvalidArgumentException('The glob pattern is not specified');
        }

        $inputDir = rtrim($inputDir, '/\\') . \DIRECTORY_SEPARATOR;
        $globPattern = $inputDir . $globPattern;

        $filesFound = FilesUtil::globFileSearch($globPattern, \GLOB_BRACE, $recursive);

        if ($filesFound === false || empty($filesFound)) {
            return $this;
        }

        $this->doAddFiles($inputDir, $filesFound, $localPath, $compressionMethod);

        return $this;
    }

    /**
     * Add files recursively from glob pattern.
     *
     * @param string   $inputDir          Input directory
     * @param string   $globPattern       glob pattern
     * @param string   $localPath         add files to this directory, or the root
     * @param int|null $compressionMethod Compression method.
     *                                    Use {@see ZipCompressionMethod::STORED},
     *                                    {@see ZipCompressionMethod::DEFLATED} or
     *                                    {@see ZipCompressionMethod::BZIP2}. If null, then auto choosing method.
     *
     * @throws ZipException
     *
     * @return ZipFile
     * @sse https://en.wikipedia.org/wiki/Glob_(programming) Glob pattern syntax
     */
    public function addFilesFromGlobRecursive($inputDir, $globPattern, $localPath = '/', $compressionMethod = null)
    {
        return $this->addGlob($inputDir, $globPattern, $localPath, true, $compressionMethod);
    }

    /**
     * Add files from regex pattern.
     *
     * @param string   $inputDir          search files in this directory
     * @param string   $regexPattern      regex pattern
     * @param string   $localPath         add files to this directory, or the root
     * @param int|null $compressionMethod Compression method.
     *                                    Use {@see ZipCompressionMethod::STORED},
     *                                    {@see ZipCompressionMethod::DEFLATED} or
     *                                    {@see ZipCompressionMethod::BZIP2}. If null, then auto choosing method.
     *
     * @throws ZipException
     *
     * @return ZipFile
     *
     * @internal param bool $recursive Recursive search
     */
    public function addFilesFromRegex($inputDir, $regexPattern, $localPath = '/', $compressionMethod = null)
    {
        return $this->addRegex($inputDir, $regexPattern, $localPath, false, $compressionMethod);
    }

    /**
     * Add files from regex pattern.
     *
     * @param string   $inputDir          search files in this directory
     * @param string   $regexPattern      regex pattern
     * @param string   $localPath         add files to this directory, or the root
     * @param bool     $recursive         recursive search
     * @param int|null $compressionMethod Compression method.
     *                                    Use {@see ZipCompressionMethod::STORED},
     *                                    {@see ZipCompressionMethod::DEFLATED} or
     *                                    {@see ZipCompressionMethod::BZIP2}.
     *                                    If null, then auto choosing method.
     *
     * @throws ZipException
     *
     * @return ZipFile
     */
    private function addRegex(
        $inputDir,
        $regexPattern,
        $localPath = '/',
        $recursive = true,
        $compressionMethod = null
    ) {
        $regexPattern = (string) $regexPattern;

        if (empty($regexPattern)) {
            throw new InvalidArgumentException('The regex pattern is not specified');
        }
        $inputDir = (string) $inputDir;

        if ($inputDir === '') {
            throw new InvalidArgumentException('The input directory is not specified');
        }

        if (!is_dir($inputDir)) {
            throw new InvalidArgumentException(sprintf('The "%s" directory does not exist.', $inputDir));
        }
        $inputDir = rtrim($inputDir, '/\\') . \DIRECTORY_SEPARATOR;

        $files = FilesUtil::regexFileSearch($inputDir, $regexPattern, $recursive);

        if (empty($files)) {
            return $this;
        }

        $this->doAddFiles($inputDir, $files, $localPath, $compressionMethod);

        return $this;
    }

    /**
     * @param string   $fileSystemDir
     * @param array    $files
     * @param string   $zipPath
     * @param int|null $compressionMethod
     *
     * @throws ZipException
     */
    private function doAddFiles($fileSystemDir, array $files, $zipPath, $compressionMethod = null)
    {
        $fileSystemDir = rtrim($fileSystemDir, '/\\') . \DIRECTORY_SEPARATOR;

        if (!empty($zipPath) && \is_string($zipPath)) {
            $zipPath = trim($zipPath, '\\/') . '/';
        } else {
            $zipPath = '/';
        }

        /**
         * @var string $file
         */
        foreach ($files as $file) {
            $filename = str_replace($fileSystemDir, $zipPath, $file);
            $filename = ltrim($filename, '\\/');

            if (is_dir($file) && FilesUtil::isEmptyDir($file)) {
                $this->addEmptyDir($filename);
            } elseif (is_file($file)) {
                $this->addFile($file, $filename, $compressionMethod);
            }
        }
    }

    /**
     * Add files recursively from regex pattern.
     *
     * @param string   $inputDir          search files in this directory
     * @param string   $regexPattern      regex pattern
     * @param string   $localPath         add files to this directory, or the root
     * @param int|null $compressionMethod Compression method.
     *                                    Use {@see ZipCompressionMethod::STORED},
     *                                    {@see ZipCompressionMethod::DEFLATED} or
     *                                    {@see ZipCompressionMethod::BZIP2}. If null, then auto choosing method.
     *
     * @throws ZipException
     *
     * @return ZipFile
     *
     * @internal param bool $recursive Recursive search
     */
    public function addFilesFromRegexRecursive($inputDir, $regexPattern, $localPath = '/', $compressionMethod = null)
    {
        return $this->addRegex($inputDir, $regexPattern, $localPath, true, $compressionMethod);
    }

    /**
     * Add array data to archive.
     * Keys is local names.
     * Values is contents.
     *
     * @param array $mapData associative array for added to zip
     */
    public function addAll(array $mapData)
    {
        foreach ($mapData as $localName => $content) {
            $this[$localName] = $content;
        }
    }

    /**
     * Rename the entry.
     *
     * @param string $oldName old entry name
     * @param string $newName new entry name
     *
     * @throws ZipException
     *
     * @return ZipFile
     */
    public function rename($oldName, $newName)
    {
        if ($oldName === null || $newName === null) {
            throw new InvalidArgumentException('name is null');
        }
        $oldName = ltrim((string) $oldName, '\\/');
        $newName = ltrim((string) $newName, '\\/');

        if ($oldName !== $newName) {
            $this->zipContainer->renameEntry($oldName, $newName);
        }

        return $this;
    }

    /**
     * Delete entry by name.
     *
     * @param string $entryName zip Entry name
     *
     * @throws ZipEntryNotFoundException if entry not found
     *
     * @return ZipFile
     */
    public function deleteFromName($entryName)
    {
        $entryName = ltrim((string) $entryName, '\\/');

        if (!$this->zipContainer->deleteEntry($entryName)) {
            throw new ZipEntryNotFoundException($entryName);
        }

        return $this;
    }

    /**
     * Delete entries by glob pattern.
     *
     * @param string $globPattern Glob pattern
     *
     * @return ZipFile
     * @sse https://en.wikipedia.org/wiki/Glob_(programming) Glob pattern syntax
     */
    public function deleteFromGlob($globPattern)
    {
        if ($globPattern === null || !\is_string($globPattern) || empty($globPattern)) {
            throw new InvalidArgumentException('The glob pattern is not specified');
        }
        $globPattern = '~' . FilesUtil::convertGlobToRegEx($globPattern) . '~si';
        $this->deleteFromRegex($globPattern);

        return $this;
    }

    /**
     * Delete entries by regex pattern.
     *
     * @param string $regexPattern Regex pattern
     *
     * @return ZipFile
     */
    public function deleteFromRegex($regexPattern)
    {
        if ($regexPattern === null || !\is_string($regexPattern) || empty($regexPattern)) {
            throw new InvalidArgumentException('The regex pattern is not specified');
        }
        $this->matcher()->match($regexPattern)->delete();

        return $this;
    }

    /**
     * Delete all entries.
     *
     * @return ZipFile
     */
    public function deleteAll()
    {
        $this->zipContainer->deleteAll();

        return $this;
    }

    /**
     * Set compression level for new entries.
     *
     * @param int $compressionLevel
     *
     * @return ZipFile
     *
     * @see ZipCompressionLevel::NORMAL
     * @see ZipCompressionLevel::SUPER_FAST
     * @see ZipCompressionLevel::FAST
     * @see ZipCompressionLevel::MAXIMUM
     */
    public function setCompressionLevel($compressionLevel = ZipCompressionLevel::NORMAL)
    {
        $compressionLevel = (int) $compressionLevel;

        foreach ($this->zipContainer->getEntries() as $entry) {
            $entry->setCompressionLevel($compressionLevel);
        }

        return $this;
    }

    /**
     * @param string $entryName
     * @param int    $compressionLevel
     *
     * @throws ZipException
     *
     * @return ZipFile
     *
     * @see ZipCompressionLevel::NORMAL
     * @see ZipCompressionLevel::SUPER_FAST
     * @see ZipCompressionLevel::FAST
     * @see ZipCompressionLevel::MAXIMUM
     */
    public function setCompressionLevelEntry($entryName, $compressionLevel)
    {
        $compressionLevel = (int) $compressionLevel;
        $this->getEntry($entryName)->setCompressionLevel($compressionLevel);

        return $this;
    }

    /**
     * @param string $entryName
     * @param int    $compressionMethod Compression method.
     *                                  Use {@see ZipCompressionMethod::STORED}, {@see ZipCompressionMethod::DEFLATED}
     *                                  or
     *                                  {@see ZipCompressionMethod::BZIP2}. If null, then auto choosing method.
     *
     * @throws ZipException
     *
     * @return ZipFile
     *
     * @see ZipCompressionMethod::STORED
     * @see ZipCompressionMethod::DEFLATED
     * @see ZipCompressionMethod::BZIP2
     */
    public function setCompressionMethodEntry($entryName, $compressionMethod)
    {
        $this->zipContainer
            ->getEntry($entryName)
            ->setCompressionMethod($compressionMethod)
        ;

        return $this;
    }

    /**
     * zipalign is optimization to Android application (APK) files.
     *
     * @param int|null $align
     *
     * @return ZipFile
     *
     * @see https://developer.android.com/studio/command-line/zipalign.html
     */
    public function setZipAlign($align = null)
    {
        $this->zipContainer->setZipAlign($align);

        return $this;
    }

    /**
     * Set password to all input encrypted entries.
     *
     * @param string $password Password
     *
     * @return ZipFile
     */
    public function setReadPassword($password)
    {
        $this->zipContainer->setReadPassword($password);

        return $this;
    }

    /**
     * Set password to concrete input entry.
     *
     * @param string $entryName
     * @param string $password  Password
     *
     * @throws ZipException
     *
     * @return ZipFile
     */
    public function setReadPasswordEntry($entryName, $password)
    {
        $this->zipContainer->setReadPasswordEntry($entryName, $password);

        return $this;
    }

    /**
     * Sets a new password for all files in the archive.
     *
     * @param string   $password         Password
     * @param int|null $encryptionMethod Encryption method
     *
     * @return ZipFile
     */
    public function setPassword($password, $encryptionMethod = ZipEncryptionMethod::WINZIP_AES_256)
    {
        $this->zipContainer->setWritePassword($password);

        if ($encryptionMethod !== null) {
            $this->zipContainer->setEncryptionMethod($encryptionMethod);
        }

        return $this;
    }

    /**
     * Sets a new password of an entry defined by its name.
     *
     * @param string   $entryName
     * @param string   $password
     * @param int|null $encryptionMethod
     *
     * @throws ZipException
     *
     * @return ZipFile
     */
    public function setPasswordEntry($entryName, $password, $encryptionMethod = null)
    {
        $this->getEntry($entryName)->setPassword($password, $encryptionMethod);

        return $this;
    }

    /**
     * Disable encryption for all entries that are already in the archive.
     *
     * @return ZipFile
     */
    public function disableEncryption()
    {
        $this->zipContainer->removePassword();

        return $this;
    }

    /**
     * Disable encryption of an entry defined by its name.
     *
     * @param string $entryName
     *
     * @return ZipFile
     */
    public function disableEncryptionEntry($entryName)
    {
        $this->zipContainer->removePasswordEntry($entryName);

        return $this;
    }

    /**
     * Undo all changes done in the archive.
     *
     * @return ZipFile
     */
    public function unchangeAll()
    {
        $this->zipContainer->unchangeAll();

        return $this;
    }

    /**
     * Undo change archive comment.
     *
     * @return ZipFile
     */
    public function unchangeArchiveComment()
    {
        $this->zipContainer->unchangeArchiveComment();

        return $this;
    }

    /**
     * Revert all changes done to an entry with the given name.
     *
     * @param string|ZipEntry $entry Entry name or ZipEntry
     *
     * @return ZipFile
     */
    public function unchangeEntry($entry)
    {
        $this->zipContainer->unchangeEntry($entry);

        return $this;
    }

    /**
     * Save as file.
     *
     * @param string $filename Output filename
     *
     * @throws ZipException
     *
     * @return ZipFile
     */
    public function saveAsFile($filename)
    {
        $filename = (string) $filename;

        $tempFilename = $filename . '.temp' . uniqid('', false);

        if (!($handle = @fopen($tempFilename, 'w+b'))) {
            throw new InvalidArgumentException(sprintf('Cannot open "%s" for writing.', $tempFilename));
        }
        $this->saveAsStream($handle);

        $reopen = false;

        if ($this->reader !== null) {
            $meta = $this->reader->getStreamMetaData();

            if ($meta['wrapper_type'] === 'plainfile' && isset($meta['uri'])) {
                $readFilePath = realpath($meta['uri']);
                $writeFilePath = realpath($filename);

                if ($readFilePath !== false && $writeFilePath !== false && $readFilePath === $writeFilePath) {
                    $this->reader->close();
                    $reopen = true;
                }
            }
        }

        if (!@rename($tempFilename, $filename)) {
            if (is_file($tempFilename)) {
                unlink($tempFilename);
            }

            throw new ZipException(sprintf('Cannot move %s to %s', $tempFilename, $filename));
        }

        if ($reopen) {
            return $this->openFile($filename);
        }

        return $this;
    }

    /**
     * Save as stream.
     *
     * @param resource $handle Output stream resource
     *
     * @throws ZipException
     *
     * @return ZipFile
     */
    public function saveAsStream($handle)
    {
        if (!\is_resource($handle)) {
            throw new InvalidArgumentException('handle is not resource');
        }
        ftruncate($handle, 0);
        $this->writeZipToStream($handle);
        fclose($handle);

        return $this;
    }

    /**
     * Output .ZIP archive as attachment.
     * Die after output.
     *
     * @param string      $outputFilename Output filename
     * @param string|null $mimeType       Mime-Type
     * @param bool        $attachment     Http Header 'Content-Disposition' if true then attachment otherwise inline
     *
     * @throws ZipException
     */
    public function outputAsAttachment($outputFilename, $mimeType = null, $attachment = true)
    {
        $outputFilename = (string) $outputFilename;

        if ($mimeType === null) {
            $mimeType = $this->getMimeTypeByFilename($outputFilename);
        }

        if (!($handle = fopen('php://temp', 'w+b'))) {
            throw new InvalidArgumentException('php://temp cannot open for write.');
        }
        $this->writeZipToStream($handle);
        $this->close();

        $size = fstat($handle)['size'];

        $headerContentDisposition = 'Content-Disposition: ' . ($attachment ? 'attachment' : 'inline');

        if (!empty($outputFilename)) {
            $headerContentDisposition .= '; filename="' . basename($outputFilename) . '"';
        }

        header($headerContentDisposition);
        header('Content-Type: ' . $mimeType);
        header('Content-Length: ' . $size);

        rewind($handle);

        try {
            echo stream_get_contents($handle, -1, 0);
        } finally {
            fclose($handle);
        }
    }

    /**
     * @param string $outputFilename
     *
     * @return string
     */
    protected function getMimeTypeByFilename($outputFilename)
    {
        $outputFilename = (string) $outputFilename;
        $ext = strtolower(pathinfo($outputFilename, \PATHINFO_EXTENSION));

        if (!empty($ext) && isset(self::$defaultMimeTypes[$ext])) {
            return self::$defaultMimeTypes[$ext];
        }

        return self::$defaultMimeTypes['zip'];
    }

    /**
     * Output .ZIP archive as PSR-7 Response.
     *
     * @param ResponseInterface $response       Instance PSR-7 Response
     * @param string            $outputFilename Output filename
     * @param string|null       $mimeType       Mime-Type
     * @param bool              $attachment     Http Header 'Content-Disposition' if true then attachment otherwise inline
     *
     * @throws ZipException
     *
     * @return ResponseInterface
     */
    public function outputAsResponse(ResponseInterface $response, $outputFilename, $mimeType = null, $attachment = true)
    {
        $outputFilename = (string) $outputFilename;

        if ($mimeType === null) {
            $mimeType = $this->getMimeTypeByFilename($outputFilename);
        }

        if (!($handle = fopen('php://temp', 'w+b'))) {
            throw new InvalidArgumentException('php://temp cannot open for write.');
        }
        $this->writeZipToStream($handle);
        $this->close();
        rewind($handle);

        $contentDispositionValue = ($attachment ? 'attachment' : 'inline');

        if (!empty($outputFilename)) {
            $contentDispositionValue .= '; filename="' . basename($outputFilename) . '"';
        }

        $stream = new ResponseStream($handle);
        $size = $stream->getSize();

        if ($size !== null) {
            /** @noinspection CallableParameterUseCaseInTypeContextInspection */
            $response = $response->withHeader('Content-Length', (string) $size);
        }

        return $response
            ->withHeader('Content-Type', $mimeType)
            ->withHeader('Content-Disposition', $contentDispositionValue)
            ->withBody($stream)
        ;
    }

    /**
     * @param resource $handle
     *
     * @throws ZipException
     */
    protected function writeZipToStream($handle)
    {
        $this->onBeforeSave();

        $this->createZipWriter()->write($handle);
    }

    /**
     * Returns the zip archive as a string.
     *
     * @throws ZipException
     *
     * @return string
     */
    public function outputAsString()
    {
        if (!($handle = fopen('php://temp', 'w+b'))) {
            throw new InvalidArgumentException('php://temp cannot open for write.');
        }
        $this->writeZipToStream($handle);
        rewind($handle);

        try {
            return stream_get_contents($handle);
        } finally {
            fclose($handle);
        }
    }

    /**
     * Event before save or output.
     */
    protected function onBeforeSave()
    {
    }

    /**
     * Close zip archive and release input stream.
     */
    public function close()
    {
        if ($this->reader !== null) {
            $this->reader->close();
            $this->reader = null;
        }
        $this->zipContainer = $this->createZipContainer(null);
        gc_collect_cycles();
    }

    /**
     * Save and reopen zip archive.
     *
     * @throws ZipException
     *
     * @return ZipFile
     */
    public function rewrite()
    {
        if ($this->reader === null) {
            throw new ZipException('input stream is null');
        }

        $meta = $this->reader->getStreamMetaData();

        if ($meta['wrapper_type'] !== 'plainfile' || !isset($meta['uri'])) {
            throw new ZipException('Overwrite is only supported for open local files.');
        }

        return $this->saveAsFile($meta['uri']);
    }

    /**
     * Release all resources.
     */
    public function __destruct()
    {
        $this->close();
    }

    /**
     * Offset to set.
     *
     * @see http://php.net/manual/en/arrayaccess.offsetset.php
     *
     * @param string                                          $entryName the offset to assign the value to
     * @param string|\DirectoryIterator|\SplFileInfo|resource $contents  the value to set
     *
     * @throws ZipException
     *
     * @see ZipFile::addFromString
     * @see ZipFile::addEmptyDir
     * @see ZipFile::addFile
     * @see ZipFile::addFilesFromIterator
     */
    public function offsetSet($entryName, $contents)
    {
        if ($entryName === null) {
            throw new InvalidArgumentException('Key must not be null, but must contain the name of the zip entry.');
        }
        $entryName = ltrim((string) $entryName, '\\/');

        if ($entryName === '') {
            throw new InvalidArgumentException('Key is empty, but must contain the name of the zip entry.');
        }

        if ($contents instanceof \DirectoryIterator) {
            $this->addFilesFromIterator($contents, $entryName);
        } elseif ($contents instanceof \SplFileInfo) {
            $this->addSplFile($contents, $entryName);
        } elseif (StringUtil::endsWith($entryName, '/')) {
            $this->addEmptyDir($entryName);
        } elseif (\is_resource($contents)) {
            $this->addFromStream($contents, $entryName);
        } else {
            $this->addFromString($entryName, (string) $contents);
        }
    }

    /**
     * Offset to unset.
     *
     * @see http://php.net/manual/en/arrayaccess.offsetunset.php
     *
     * @param string $entryName the offset to unset
     *
     * @throws ZipEntryNotFoundException
     */
    public function offsetUnset($entryName)
    {
        $this->deleteFromName($entryName);
    }

    /**
     * Return the current element.
     *
     * @see http://php.net/manual/en/iterator.current.php
     *
     * @throws ZipException
     *
     * @return mixed can return any type
     *
     * @since 5.0.0
     */
    public function current()
    {
        return $this->offsetGet($this->key());
    }

    /**
     * Offset to retrieve.
     *
     * @see http://php.net/manual/en/arrayaccess.offsetget.php
     *
     * @param string $entryName the offset to retrieve
     *
     * @throws ZipException
     *
     * @return string|null
     */
    public function offsetGet($entryName)
    {
        return $this->getEntryContents($entryName);
    }

    /**
     * Return the key of the current element.
     *
     * @see http://php.net/manual/en/iterator.key.php
     *
     * @return mixed scalar on success, or null on failure
     *
     * @since 5.0.0
     */
    public function key()
    {
        return key($this->zipContainer->getEntries());
    }

    /**
     * Move forward to next element.
     *
     * @see http://php.net/manual/en/iterator.next.php
     * @since 5.0.0
     */
    public function next()
    {
        next($this->zipContainer->getEntries());
    }

    /**
     * Checks if current position is valid.
     *
     * @see http://php.net/manual/en/iterator.valid.php
     *
     * @return bool The return value will be casted to boolean and then evaluated.
     *              Returns true on success or false on failure.
     *
     * @since 5.0.0
     */
    public function valid()
    {
        return $this->offsetExists($this->key());
    }

    /**
     * Whether a offset exists.
     *
     * @see http://php.net/manual/en/arrayaccess.offsetexists.php
     *
     * @param string $entryName an offset to check for
     *
     * @return bool true on success or false on failure.
     *              The return value will be casted to boolean if non-boolean was returned.
     */
    public function offsetExists($entryName)
    {
        return $this->hasEntry($entryName);
    }

    /**
     * Rewind the Iterator to the first element.
     *
     * @see http://php.net/manual/en/iterator.rewind.php
     * @since 5.0.0
     */
    public function rewind()
    {
        reset($this->zipContainer->getEntries());
    }
}
