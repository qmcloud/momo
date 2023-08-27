<?php

namespace PhpZip;

use PhpZip\Constants\ZipCompressionLevel;
use PhpZip\Constants\ZipCompressionMethod;
use PhpZip\Constants\ZipEncryptionMethod;
use PhpZip\Exception\ZipEntryNotFoundException;
use PhpZip\Exception\ZipException;
use PhpZip\Model\ZipEntry;
use PhpZip\Model\ZipEntryMatcher;
use PhpZip\Model\ZipInfo;
use Psr\Http\Message\ResponseInterface;
use Symfony\Component\Finder\Finder;

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
 *
 * @deprecated will be removed in version 4.0. Use the {@see ZipFile} class.
 */
interface ZipFileInterface extends \Countable, \ArrayAccess, \Iterator
{
    /**
     * Method for Stored (uncompressed) entries.
     *
     * @see ZipEntry::setCompressionMethod()
     * @deprecated Use {@see ZipCompressionMethod::STORED}
     */
    const METHOD_STORED = ZipCompressionMethod::STORED;

    /**
     * Method for Deflated compressed entries.
     *
     * @see ZipEntry::setCompressionMethod()
     * @deprecated Use {@see ZipCompressionMethod::DEFLATED}
     */
    const METHOD_DEFLATED = ZipCompressionMethod::DEFLATED;

    /**
     * Method for BZIP2 compressed entries.
     * Require php extension bz2.
     *
     * @see ZipEntry::setCompressionMethod()
     * @deprecated Use {@see ZipCompressionMethod::BZIP2}
     */
    const METHOD_BZIP2 = ZipCompressionMethod::BZIP2;

    /**
     * @var int default compression level
     *
     * @deprecated Use {@see ZipCompressionLevel::NORMAL}
     */
    const LEVEL_DEFAULT_COMPRESSION = ZipCompressionLevel::NORMAL;

    /**
     * Compression level for fastest compression.
     *
     * @deprecated Use {@see ZipCompressionLevel::FAST}
     */
    const LEVEL_FAST = ZipCompressionLevel::FAST;

    /**
     * Compression level for fastest compression.
     *
     * @deprecated Use {@see ZipCompressionLevel::SUPER_FAST}
     */
    const LEVEL_BEST_SPEED = ZipCompressionLevel::SUPER_FAST;

    /** @deprecated Use {@see ZipCompressionLevel::SUPER_FAST} */
    const LEVEL_SUPER_FAST = ZipCompressionLevel::SUPER_FAST;

    /**
     * Compression level for best compression.
     *
     * @deprecated Use {@see ZipCompressionLevel::MAXIMUM}
     */
    const LEVEL_BEST_COMPRESSION = ZipCompressionLevel::MAXIMUM;

    /**
     * No specified method for set encryption method to Traditional PKWARE encryption.
     *
     * @deprecated Use {@see ZipEncryptionMethod::PKWARE}
     */
    const ENCRYPTION_METHOD_TRADITIONAL = ZipEncryptionMethod::PKWARE;

    /**
     * No specified method for set encryption method to WinZip AES encryption.
     * Default value 256 bit.
     *
     * @deprecated Use {@see ZipEncryptionMethod::WINZIP_AES_256}
     */
    const ENCRYPTION_METHOD_WINZIP_AES = ZipEncryptionMethod::WINZIP_AES_256;

    /**
     * No specified method for set encryption method to WinZip AES encryption 128 bit.
     *
     * @deprecated Use {@see ZipEncryptionMethod::WINZIP_AES_128}
     */
    const ENCRYPTION_METHOD_WINZIP_AES_128 = ZipEncryptionMethod::WINZIP_AES_128;

    /**
     * No specified method for set encryption method to WinZip AES encryption 194 bit.
     *
     * @deprecated Use {@see ZipEncryptionMethod::WINZIP_AES_192}
     */
    const ENCRYPTION_METHOD_WINZIP_AES_192 = ZipEncryptionMethod::WINZIP_AES_192;

    /**
     * No specified method for set encryption method to WinZip AES encryption 256 bit.
     *
     * @deprecated Use {@see ZipEncryptionMethod::WINZIP_AES_256}
     */
    const ENCRYPTION_METHOD_WINZIP_AES_256 = ZipEncryptionMethod::WINZIP_AES_256;

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
    public function openFile($filename, array $options = []);

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
    public function openFromString($data, array $options = []);

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
    public function openFromStream($handle, array $options = []);

    /**
     * @return string[] returns the list files
     */
    public function getListFiles();

    /**
     * @return int returns the number of entries in this ZIP file
     */
    public function count();

    /**
     * Returns the file comment.
     *
     * @return string|null the file comment
     */
    public function getArchiveComment();

    /**
     * Set archive comment.
     *
     * @param string|null $comment
     *
     * @return ZipFile
     */
    public function setArchiveComment($comment = null);

    /**
     * Checks if there is an entry in the archive.
     *
     * @param string $entryName
     *
     * @return bool
     */
    public function hasEntry($entryName);

    /**
     * Returns ZipEntry object.
     *
     * @param string $entryName
     *
     * @throws ZipEntryNotFoundException
     *
     * @return ZipEntry
     */
    public function getEntry($entryName);

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
    public function isDirectory($entryName);

    /**
     * Returns entry comment.
     *
     * @param string $entryName
     *
     * @throws ZipException
     * @throws ZipEntryNotFoundException
     *
     * @return string
     */
    public function getEntryComment($entryName);

    /**
     * Set entry comment.
     *
     * @param string      $entryName
     * @param string|null $comment
     *
     * @throws ZipEntryNotFoundException
     * @throws ZipException
     *
     * @return ZipFile
     */
    public function setEntryComment($entryName, $comment = null);

    /**
     * Returns the entry contents.
     *
     * @param string $entryName
     *
     * @throws ZipEntryNotFoundException
     * @throws ZipException
     *
     * @return string
     */
    public function getEntryContents($entryName);

    /**
     * @param string $entryName
     *
     * @throws ZipEntryNotFoundException
     * @throws ZipException
     *
     * @return resource
     */
    public function getEntryStream($entryName);

    /**
     * Get info by entry.
     *
     * @param string|ZipEntry $entryName
     *
     * @throws ZipException
     * @throws ZipEntryNotFoundException
     *
     * @return ZipInfo
     */
    public function getEntryInfo($entryName);

    /**
     * Get info by all entries.
     *
     * @return ZipInfo[]
     */
    public function getAllInfo();

    /**
     * @return ZipEntryMatcher
     */
    public function matcher();

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
    public function extractTo($destDir, $entries = null, array $options = [], &$extractedEntries = []);

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
    public function addFromString($entryName, $contents, $compressionMethod = null);

    /**
     * @param Finder $finder
     * @param array  $options
     *
     * @throws ZipException
     *
     * @return ZipEntry[]
     */
    public function addFromFinder(Finder $finder, array $options = []);

    /**
     * @param \SplFileInfo $file
     * @param string|null  $entryName
     * @param array        $options
     *
     * @throws ZipException
     *
     * @return ZipEntry
     */
    public function addSplFile(\SplFileInfo $file, $entryName = null, array $options = []);

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
    public function addFile($filename, $entryName = null, $compressionMethod = null);

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
    public function addFromStream($stream, $entryName, $compressionMethod = null);

    /**
     * Add an empty directory in the zip archive.
     *
     * @param string $dirName
     *
     * @throws ZipException
     *
     * @return ZipFile
     */
    public function addEmptyDir($dirName);

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
    public function addDir($inputDir, $localPath = '/', $compressionMethod = null);

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
    public function addDirRecursive($inputDir, $localPath = '/', $compressionMethod = null);

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
    public function addFilesFromIterator(\Iterator $iterator, $localPath = '/', $compressionMethod = null);

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
    public function addFilesFromGlob($inputDir, $globPattern, $localPath = '/', $compressionMethod = null);

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
    public function addFilesFromGlobRecursive($inputDir, $globPattern, $localPath = '/', $compressionMethod = null);

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
    public function addFilesFromRegex($inputDir, $regexPattern, $localPath = '/', $compressionMethod = null);

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
    public function addFilesFromRegexRecursive($inputDir, $regexPattern, $localPath = '/', $compressionMethod = null);

    /**
     * Add array data to archive.
     * Keys is local names.
     * Values is contents.
     *
     * @param array $mapData associative array for added to zip
     */
    public function addAll(array $mapData);

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
    public function rename($oldName, $newName);

    /**
     * Delete entry by name.
     *
     * @param string $entryName zip Entry name
     *
     * @throws ZipEntryNotFoundException if entry not found
     *
     * @return ZipFile
     */
    public function deleteFromName($entryName);

    /**
     * Delete entries by glob pattern.
     *
     * @param string $globPattern Glob pattern
     *
     * @return ZipFile
     * @sse https://en.wikipedia.org/wiki/Glob_(programming) Glob pattern syntax
     */
    public function deleteFromGlob($globPattern);

    /**
     * Delete entries by regex pattern.
     *
     * @param string $regexPattern Regex pattern
     *
     * @return ZipFile
     */
    public function deleteFromRegex($regexPattern);

    /**
     * Delete all entries.
     *
     * @return ZipFile
     */
    public function deleteAll();

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
    public function setCompressionLevel($compressionLevel = ZipCompressionLevel::NORMAL);

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
    public function setCompressionLevelEntry($entryName, $compressionLevel);

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
    public function setCompressionMethodEntry($entryName, $compressionMethod);

    /**
     * zipalign is optimization to Android application (APK) files.
     *
     * @param int|null $align
     *
     * @return ZipFile
     *
     * @see https://developer.android.com/studio/command-line/zipalign.html
     */
    public function setZipAlign($align = null);

    /**
     * Set password to all input encrypted entries.
     *
     * @param string $password Password
     *
     * @return ZipFile
     */
    public function setReadPassword($password);

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
    public function setReadPasswordEntry($entryName, $password);

    /**
     * Sets a new password for all files in the archive.
     *
     * @param string   $password         Password
     * @param int|null $encryptionMethod Encryption method
     *
     * @return ZipFile
     */
    public function setPassword($password, $encryptionMethod = ZipEncryptionMethod::WINZIP_AES_256);

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
    public function setPasswordEntry($entryName, $password, $encryptionMethod = null);

    /**
     * Disable encryption for all entries that are already in the archive.
     *
     * @return ZipFile
     */
    public function disableEncryption();

    /**
     * Disable encryption of an entry defined by its name.
     *
     * @param string $entryName
     *
     * @return ZipFile
     */
    public function disableEncryptionEntry($entryName);

    /**
     * Undo all changes done in the archive.
     *
     * @return ZipFile
     */
    public function unchangeAll();

    /**
     * Undo change archive comment.
     *
     * @return ZipFile
     */
    public function unchangeArchiveComment();

    /**
     * Revert all changes done to an entry with the given name.
     *
     * @param string|ZipEntry $entry Entry name or ZipEntry
     *
     * @return ZipFile
     */
    public function unchangeEntry($entry);

    /**
     * Save as file.
     *
     * @param string $filename Output filename
     *
     * @throws ZipException
     *
     * @return ZipFile
     */
    public function saveAsFile($filename);

    /**
     * Save as stream.
     *
     * @param resource $handle Output stream resource
     *
     * @throws ZipException
     *
     * @return ZipFile
     */
    public function saveAsStream($handle);

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
    public function outputAsAttachment($outputFilename, $mimeType = null, $attachment = true);

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
    public function outputAsResponse(
        ResponseInterface $response,
        $outputFilename,
        $mimeType = null,
        $attachment = true
    );

    /**
     * Returns the zip archive as a string.
     *
     * @throws ZipException
     *
     * @return string
     */
    public function outputAsString();

    /**
     * Close zip archive and release input stream.
     */
    public function close();

    /**
     * Save and reopen zip archive.
     *
     * @throws ZipException
     *
     * @return ZipFile
     */
    public function rewrite();

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
    public function offsetSet($entryName, $contents);

    /**
     * Offset to unset.
     *
     * @see http://php.net/manual/en/arrayaccess.offsetunset.php
     *
     * @param string $entryName the offset to unset
     *
     * @throws ZipEntryNotFoundException
     */
    public function offsetUnset($entryName);

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
    public function current();

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
    public function offsetGet($entryName);

    /**
     * Return the key of the current element.
     *
     * @see http://php.net/manual/en/iterator.key.php
     *
     * @return mixed scalar on success, or null on failure
     *
     * @since 5.0.0
     */
    public function key();

    /**
     * Move forward to next element.
     *
     * @see http://php.net/manual/en/iterator.next.php
     * @since 5.0.0
     */
    public function next();

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
    public function valid();

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
    public function offsetExists($entryName);

    /**
     * Rewind the Iterator to the first element.
     *
     * @see http://php.net/manual/en/iterator.rewind.php
     * @since 5.0.0
     */
    public function rewind();
}
