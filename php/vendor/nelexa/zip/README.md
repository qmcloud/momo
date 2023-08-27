`PhpZip`
========
`PhpZip` is a php-library for extended work with ZIP-archives.

[![Build Status](https://travis-ci.org/Ne-Lexa/php-zip.svg?branch=master)](https://travis-ci.org/Ne-Lexa/php-zip)
[![Code Coverage](https://scrutinizer-ci.com/g/Ne-Lexa/php-zip/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/Ne-Lexa/php-zip/?branch=master)
[![Latest Stable Version](https://poser.pugx.org/nelexa/zip/v/stable)](https://packagist.org/packages/nelexa/zip)
[![Total Downloads](https://poser.pugx.org/nelexa/zip/downloads)](https://packagist.org/packages/nelexa/zip)
[![Minimum PHP Version](http://img.shields.io/badge/php-%3E%3D%205.5-8892BF.svg)](https://php.net/)
[![License](https://poser.pugx.org/nelexa/zip/license)](https://packagist.org/packages/nelexa/zip)

[Russian Documentation](README.RU.md)

Table of contents
-----------------
- [Features](#Features)
- [Requirements](#Requirements)
- [Installation](#Installation)
- [Examples](#Examples)
- [Glossary](#Glossary)
- [Documentation](#Documentation)
  + [Overview of methods of the class `\PhpZip\ZipFile`](#Documentation-Overview)
  + [Creation/Opening of ZIP-archive](#Documentation-Open-Zip-Archive)
  + [Reading entries from the archive](#Documentation-Open-Zip-Entries)
  + [Iterating entries](#Documentation-Zip-Iterate)
  + [Getting information about entries](#Documentation-Zip-Info)
  + [Adding entries to the archive](#Documentation-Add-Zip-Entries)
  + [Deleting entries from the archive](#Documentation-Remove-Zip-Entries)
  + [Working with entries and archive](#Documentation-Entries)
  + [Working with passwords](#Documentation-Password)
  + [zipalign - alignment tool for Android (APK) files](#Documentation-ZipAlign-Usage)
  + [Undo changes](#Documentation-Unchanged)
  + [Saving a file or output to a browser](#Documentation-Save-Or-Output-Entries)
  + [Closing the archive](#Documentation-Close-Zip-Archive)
- [Running the tests](#Running-Tests)
- [Changelog](#Changelog)
- [Upgrade](#Upgrade)
  + [Upgrade version 2 to version 3.0](#Upgrade-v2-to-v3)

### <a name="Features"></a> Features
- Opening and unzipping zip files.
- Creating ZIP-archives.
- Modifying ZIP archives.
- Pure php (not require extension `php-zip` and class `\ZipArchive`).
- It supports saving the archive to a file, outputting the archive to the browser, or outputting it as a string without saving it to a file.
- Archival comments and comments of individual entry are supported.
- Get information about each entry in the archive.
- Only the following compression methods are supported:
  + No compressed (Stored).
  + Deflate compression.
  + BZIP2 compression with the extension `php-bz2`.
- Support for `ZIP64` (file size is more than 4 GB or the number of entries in the archive is more than 65535).
- Built-in support for aligning the archive to optimize Android packages (APK) [`zipalign`](https://developer.android.com/studio/command-line/zipalign.html).
- Working with passwords for PHP 5.5
  > **Attention!**
  >
  > For 32-bit systems, the `Traditional PKWARE Encryption (ZipCrypto)` encryption method is not currently supported. 
  > Use the encryption method `WinZIP AES Encryption`, whenever possible.
  + Set the password to read the archive for all entries or only for some.
  + Change the password for the archive, including for individual entries.
  + Delete the archive password for all or individual entries.
  + Set the password and/or the encryption method, both for all, and for individual entries in the archive.
  + Set different passwords and encryption methods for different entries.
  + Delete the password for all or some entries.
  + Support `Traditional PKWARE Encryption (ZipCrypto)` and `WinZIP AES Encryption` encryption methods.
  + Set the encryption method for all or individual entries in the archive.

### <a name="Requirements"></a> Requirements
- `PHP` >= 5.5 (preferably 64-bit).
- Optional php-extension `bzip2` for BZIP2 compression.
- Optional php-extension `openssl` or `mcrypt` for `WinZip Aes Encryption` support.

### <a name="Installation"></a> Installation
`composer require nelexa/zip`

Latest stable version: [![Latest Stable Version](https://poser.pugx.org/nelexa/zip/v/stable)](https://packagist.org/packages/nelexa/zip)

### <a name="Examples"></a> Examples
```php
// create new archive
$zipFile = new \PhpZip\ZipFile();
try{
    $zipFile
        ->addFromString('zip/entry/filename', 'Is file content') // add an entry from the string
        ->addFile('/path/to/file', 'data/tofile') // add an entry from the file
        ->addDir(__DIR__, 'to/path/') // add files from the directory
        ->saveAsFile($outputFilename) // save the archive to a file
        ->close(); // close archive
            
    // open archive, extract, add files, set password and output to browser.
    $zipFile
        ->openFile($outputFilename) // open archive from file
        ->extractTo($outputDirExtract) // extract files to the specified directory
        ->deleteFromRegex('~^\.~') // delete all hidden (Unix) files
        ->addFromString('dir/file.txt', 'Test file') // add a new entry from the string
        ->setPassword('password') // set password for all entries
        ->outputAsAttachment('library.jar'); // output to the browser without saving to a file
}
catch(\PhpZip\Exception\ZipException $e){
    // handle exception
}
finally{
    $zipFile->close();
}
```
Other examples can be found in the `tests/` folder

### <a name="Glossary"></a> Glossary
**Zip Entry** - file or folder in a ZIP-archive. Each entry in the archive has certain properties, for example: file name, compression method, encryption method, file size before compression, file size after compression, CRC32 and others.

### <a name="Documentation"></a> Documentation:
#### <a name="Documentation-Overview"></a> Overview of methods of the class `\PhpZip\ZipFile`
- [ZipFile::__construct](#Documentation-ZipFile-__construct) - initializes the ZIP archive.
- [ZipFile::addAll](#Documentation-ZipFile-addAll) - adds all entries from an array.
- [ZipFile::addDir](#Documentation-ZipFile-addDir) - adds files to the archive from the directory on the specified path without subdirectories.
- [ZipFile::addDirRecursive](#Documentation-ZipFile-addDirRecursive) - adds files to the archive from the directory on the specified path with subdirectories.
- [ZipFile::addEmptyDir](#Documentation-ZipFile-addEmptyDir) - add a new directory.
- [ZipFile::addFile](#Documentation-ZipFile-addFile) - adds a file to a ZIP archive from the given path.
- [ZipFile::addSplFile](#Documentation-ZipFile-addSplFile) - adds a `\SplFileInfo` to a ZIP archive.
- [ZipFile::addFromFinder](#Documentation-ZipFile-addFromFinder) - adds files from the `Symfony\Component\Finder\Finder` to a ZIP archive.
- [ZipFile::addFilesFromIterator](#Documentation-ZipFile-addFilesFromIterator) - adds files from the iterator of directories.
- [ZipFile::addFilesFromGlob](#Documentation-ZipFile-addFilesFromGlob) - adds files from a directory by glob pattern without subdirectories.
- [ZipFile::addFilesFromGlobRecursive](#Documentation-ZipFile-addFilesFromGlobRecursive) - adds files from a directory by glob pattern with subdirectories.
- [ZipFile::addFilesFromRegex](#Documentation-ZipFile-addFilesFromRegex) - adds files from a directory by PCRE pattern without subdirectories.
- [ZipFile::addFilesFromRegexRecursive](#Documentation-ZipFile-addFilesFromRegexRecursive) - adds files from a directory by PCRE pattern with subdirectories.
- [ZipFile::addFromStream](#Documentation-ZipFile-addFromStream) - adds a entry from the stream to the ZIP archive.
- [ZipFile::addFromString](#Documentation-ZipFile-addFromString) - adds a file to a ZIP archive using its contents.
- [ZipFile::close](#Documentation-ZipFile-close) - close the archive.
- [ZipFile::count](#Documentation-ZipFile-count) - returns the number of entries in the archive.
- [ZipFile::deleteFromName](#Documentation-ZipFile-deleteFromName) - deletes an entry in the archive using its name.
- [ZipFile::deleteFromGlob](#Documentation-ZipFile-deleteFromGlob) - deletes a entries in the archive using glob pattern.
- [ZipFile::deleteFromRegex](#Documentation-ZipFile-deleteFromRegex) - deletes a entries in the archive using PCRE pattern.
- [ZipFile::deleteAll](#Documentation-ZipFile-deleteAll) - deletes all entries in the ZIP archive.
- [ZipFile::disableEncryption](#Documentation-ZipFile-disableEncryption) - disable encryption for all entries that are already in the archive.
- [ZipFile::disableEncryptionEntry](#Documentation-ZipFile-disableEncryptionEntry) - disable encryption of an entry defined by its name.
- [ZipFile::extractTo](#Documentation-ZipFile-extractTo) - extract the archive contents.
- [ZipFile::getAllInfo](#Documentation-ZipFile-getAllInfo) - returns detailed information about all entries in the archive.
- [ZipFile::getArchiveComment](#Documentation-ZipFile-getArchiveComment) - returns the Zip archive comment.
- [ZipFile::getEntryComment](#Documentation-ZipFile-getEntryComment) - returns the comment of an entry using the entry name.
- [ZipFile::getEntryContent](#Documentation-ZipFile-getEntryContent) - returns the entry contents using its name.
- [ZipFile::getEntryInfo](#Documentation-ZipFile-getEntryInfo) - returns detailed information about the entry in the archive.
- [ZipFile::getListFiles](#Documentation-ZipFile-getListFiles) - returns list of archive files.
- [ZipFile::hasEntry](#Documentation-ZipFile-hasEntry) - checks if there is an entry in the archive.
- [ZipFile::isDirectory](#Documentation-ZipFile-isDirectory) - checks that the entry in the archive is a directory.
- [ZipFile::matcher](#Documentation-ZipFile-matcher) - selecting entries in the archive to perform operations on them.
- [ZipFile::openFile](#Documentation-ZipFile-openFile) - opens a zip-archive from a file.
- [ZipFile::openFromString](#Documentation-ZipFile-openFromString) - opens a zip-archive from a string.
- [ZipFile::openFromStream](#Documentation-ZipFile-openFromStream) - opens a zip-archive from the stream.
- [ZipFile::outputAsAttachment](#Documentation-ZipFile-outputAsAttachment) - outputs a ZIP-archive to the browser.
- [ZipFile::outputAsResponse](#Documentation-ZipFile-outputAsResponse) - outputs a ZIP-archive as PSR-7 Response.
- [ZipFile::outputAsString](#Documentation-ZipFile-outputAsString) - outputs a ZIP-archive as string.
- [ZipFile::rename](#Documentation-ZipFile-rename) - renames an entry defined by its name.
- [ZipFile::rewrite](#Documentation-ZipFile-rewrite) - save changes and re-open the changed archive.
- [ZipFile::saveAsFile](#Documentation-ZipFile-saveAsFile) - saves the archive to a file.
- [ZipFile::saveAsStream](#Documentation-ZipFile-saveAsStream) - writes the archive to the stream.
- [ZipFile::setArchiveComment](#Documentation-ZipFile-setArchiveComment) - set the comment of a ZIP archive.
- [ZipFile::setCompressionLevel](#Documentation-ZipFile-setCompressionLevel) - set the compression level for all files in the archive.
- [ZipFile::setCompressionLevelEntry](#Documentation-ZipFile-setCompressionLevelEntry) - sets the compression level for the entry by its name.
- [ZipFile::setCompressionMethodEntry](#Documentation-ZipFile-setCompressionMethodEntry) - sets the compression method for the entry by its name.
- [ZipFile::setEntryComment](#Documentation-ZipFile-setEntryComment) - set the comment of an entry defined by its name.
- [ZipFile::setReadPassword](#Documentation-ZipFile-setReadPassword) - set the password for the open archive.
- [ZipFile::setReadPasswordEntry](#Documentation-ZipFile-setReadPasswordEntry) - sets a password for reading of an entry defined by its name.
- ~~ZipFile::withNewPassword~~ - is an deprecated method, use the [ZipFile::setPassword](#Documentation-ZipFile-setPassword) method.
- [ZipFile::setPassword](#Documentation-ZipFile-setPassword) - sets a new password for all files in the archive.
- [ZipFile::setPasswordEntry](#Documentation-ZipFile-setPasswordEntry) - sets a new password of an entry defined by its name.
- [ZipFile::setZipAlign](#Documentation-ZipFile-setZipAlign) - sets the alignment of the archive to optimize APK files (Android packages).
- [ZipFile::unchangeAll](#Documentation-ZipFile-unchangeAll) - undo all changes done in the archive.
- [ZipFile::unchangeArchiveComment](#Documentation-ZipFile-unchangeArchiveComment) - undo changes to the archive comment.
- [ZipFile::unchangeEntry](#Documentation-ZipFile-unchangeEntry) - undo changes of an entry defined by its name.
- ~~ZipFile::withoutPassword~~ - is an deprecated method, use the [ZipFile::disableEncryption](#Documentation-ZipFile-disableEncryption) method.
- ~~ZipFile::withReadPassword~~ - is an deprecated method, use the [ZipFile::setReadPassword](#Documentation-ZipFile-setReadPassword) method.

#### <a name="Documentation-Open-Zip-Archive"></a> Creation/Opening of ZIP-archive
<a name="Documentation-ZipFile-__construct"></a>**ZipFile::__construct** - initializes the ZIP archive.
```php
$zipFile = new \PhpZip\ZipFile();
```
<a name="Documentation-ZipFile-openFile"></a> **ZipFile::openFile** - opens a zip-archive from a file.
```php
$zipFile = new \PhpZip\ZipFile();
$zipFile->openFile('file.zip');
```
<a name="Documentation-ZipFile-openFromString"></a> **ZipFile::openFromString** - opens a zip-archive from a string.
```php
$zipFile = new \PhpZip\ZipFile();
$zipFile->openFromString($stringContents);
```
<a name="Documentation-ZipFile-openFromStream"></a> **ZipFile::openFromStream** - opens a zip-archive from the stream.
```php
$stream = fopen('file.zip', 'rb');

$zipFile = new \PhpZip\ZipFile();
$zipFile->openFromStream($stream);
```
#### <a name="Documentation-Open-Zip-Entries"></a> Reading entries from the archive
<a name="Documentation-ZipFile-count"></a> **ZipFile::count** - returns the number of entries in the archive.
```php
$zipFile = new \PhpZip\ZipFile();

$count = count($zipFile);
// or
$count = $zipFile->count();
```
<a name="Documentation-ZipFile-getListFiles"></a> **ZipFile::getListFiles** - returns list of archive files.
```php
$zipFile = new \PhpZip\ZipFile();
$listFiles = $zipFile->getListFiles();

// example array contents:
// array (
//   0 => 'info.txt',
//   1 => 'path/to/file.jpg',
//   2 => 'another path/',
//   3 => '0',
// )
```
<a name="Documentation-ZipFile-getEntryContent"></a> **ZipFile::getEntryContent** - returns the entry contents using its name.
```php
// $entryName = 'path/to/example-entry-name.txt';
$zipFile = new \PhpZip\ZipFile();

$contents = $zipFile[$entryName];
// or
$contents = $zipFile->getEntryContents($entryName);
```
<a name="Documentation-ZipFile-hasEntry"></a> **ZipFile::hasEntry** - checks if there is an entry in the archive.
```php
// $entryName = 'path/to/example-entry-name.txt';
$zipFile = new \PhpZip\ZipFile();

$hasEntry = isset($zipFile[$entryName]);
// or
$hasEntry = $zipFile->hasEntry($entryName);
```
<a name="Documentation-ZipFile-isDirectory"></a> **ZipFile::isDirectory** - checks that the entry in the archive is a directory.
```php
// $entryName = 'path/to/';
$zipFile = new \PhpZip\ZipFile();

$isDirectory = $zipFile->isDirectory($entryName);
```
<a name="Documentation-ZipFile-extractTo"></a> **ZipFile::extractTo** - extract the archive contents.
The directory must exist.
```php
$zipFile = new \PhpZip\ZipFile();
$zipFile->extractTo($directory);
```
Extract some files to the directory.
The directory must exist.
```php
// $toDirectory = '/tmp';
$extractOnlyFiles = [
    'filename1', 
    'filename2', 
    'dir/dir/dir/'
];
$zipFile = new \PhpZip\ZipFile();
$zipFile->extractTo($toDirectory, $extractOnlyFiles);
```
#### <a name="Documentation-Zip-Iterate"></a> Iterating entries
`ZipFile` is an iterator.
Can iterate all the entries in the `foreach` loop.
```php
foreach($zipFile as $entryName => $contents){
    echo "Filename: $entryName" . PHP_EOL;
    echo "Contents: $contents" . PHP_EOL;
    echo '-----------------------------' . PHP_EOL;
}
```
Can iterate through the `Iterator`.
```php
$iterator = new \ArrayIterator($zipFile);
while ($iterator->valid())
{
    $entryName = $iterator->key();
    $contents = $iterator->current();

    echo "Filename: $entryName" . PHP_EOL;
    echo "Contents: $contents" . PHP_EOL;
    echo '-----------------------------' . PHP_EOL;

    $iterator->next();
}
```
#### <a name="Documentation-Zip-Info"></a> Getting information about entries
<a name="Documentation-ZipFile-getArchiveComment"></a> **ZipFile::getArchiveComment** - returns the Zip archive comment.
```php
$zipFile = new \PhpZip\ZipFile();
$commentArchive = $zipFile->getArchiveComment();
```
<a name="Documentation-ZipFile-getEntryComment"></a> **ZipFile::getEntryComment** - returns the comment of an entry using the entry name.
```php
$zipFile = new \PhpZip\ZipFile();
$commentEntry = $zipFile->getEntryComment($entryName);
```
<a name="Documentation-ZipFile-getEntryInfo"></a> **ZipFile::getEntryInfo** - returns detailed information about the entry in the archive
```php
$zipFile = new \PhpZip\ZipFile();
$zipInfo = $zipFile->getEntryInfo('file.txt');
```
<a name="Documentation-ZipFile-getAllInfo"></a> **ZipFile::getAllInfo** - returns detailed information about all entries in the archive.
```php
$zipAllInfo = $zipFile->getAllInfo();
```
#### <a name="Documentation-Add-Zip-Entries"></a> Adding entries to the archive

All methods of adding entries to a ZIP archive allow you to specify a method for compressing content.

The following methods of compression are available:
- `\PhpZip\Constants\ZipCompressionMethod::STORED` - no compression
- `\PhpZip\Constants\ZipCompressionMethod::DEFLATED` - Deflate compression
- `\PhpZip\Constants\ZipCompressionMethod::BZIP2` - Bzip2 compression with the extension `ext-bz2`

<a name="Documentation-ZipFile-addFile"></a> **ZipFile::addFile** - adds a file to a ZIP archive from the given path.
```php
$zipFile = new \PhpZip\ZipFile();
// $file = '...../file.ext'; 
// $entryName = 'file2.ext'
$zipFile->addFile($file);

// you can specify the name of the entry in the archive (if null, then the last component from the file name is used)
$zipFile->addFile($file, $entryName);

// you can specify a compression method
$zipFile->addFile($file, $entryName, \PhpZip\Constants\ZipCompressionMethod::STORED); // No compression
$zipFile->addFile($file, $entryName, \PhpZip\Constants\ZipCompressionMethod::DEFLATED); // Deflate compression
$zipFile->addFile($file, $entryName, \PhpZip\Constants\ZipCompressionMethod::BZIP2); // BZIP2 compression
```
<a name="Documentation-ZipFile-addSplFile"></a>
**ZipFile::addSplFile"** - adds a `\SplFileInfo` to a ZIP archive.
```php
// $file = '...../file.ext'; 
// $entryName = 'file2.ext'
$zipFile = new \PhpZip\ZipFile();

$splFile = new \SplFileInfo('README.md');

$zipFile->addSplFile($splFile);
$zipFile->addSplFile($splFile, $entryName);
// or
$zipFile[$entryName] = new \SplFileInfo($file);

// set compression method
$zipFile->addSplFile($splFile, $entryName, $options = [
    \PhpZip\Constants\ZipOptions::COMPRESSION_METHOD => \PhpZip\Constants\ZipCompressionMethod::DEFLATED,
]);
```
<a name="Documentation-ZipFile-addFromFinder"></a>
**ZipFile::addFromFinder"** - adds files from the `Symfony\Component\Finder\Finder` to a ZIP archive.
https://symfony.com/doc/current/components/finder.html
```php
$finder = new \Symfony\Component\Finder\Finder();
$finder
    ->files()
    ->name('*.{jpg,jpeg,gif,png}')
    ->name('/^[0-9a-f]\./')
    ->contains('/lorem\s+ipsum$/i')
    ->in('path');

$zipFile = new \PhpZip\ZipFile();
$zipFile->addFromFinder($finder, $options = [
    \PhpZip\Constants\ZipOptions::COMPRESSION_METHOD => \PhpZip\Constants\ZipCompressionMethod::DEFLATED,
    \PhpZip\Constants\ZipOptions::MODIFIED_TIME => new \DateTimeImmutable('-1 day 5 min')
]);
```
<a name="Documentation-ZipFile-addFromString"></a> **ZipFile::addFromString** - adds a file to a ZIP archive using its contents.
```php
$zipFile = new \PhpZip\ZipFile();

$zipFile[$entryName] = $contents;
// or
$zipFile->addFromString($entryName, $contents);

// you can specify a compression method
$zipFile->addFromString($entryName, $contents, \PhpZip\Constants\ZipCompressionMethod::STORED); // No compression
$zipFile->addFromString($entryName, $contents, \PhpZip\Constants\ZipCompressionMethod::DEFLATED); // Deflate compression
$zipFile->addFromString($entryName, $contents, \PhpZip\Constants\ZipCompressionMethod::BZIP2); // BZIP2 compression
```
<a name="Documentation-ZipFile-addFromStream"></a> **ZipFile::addFromStream** - adds a entry from the stream to the ZIP archive.
```php
$zipFile = new \PhpZip\ZipFile();
// $stream = fopen(..., 'rb');

$zipFile->addFromStream($stream, $entryName);
// or
$zipFile[$entryName] = $stream;

// you can specify a compression method
$zipFile->addFromStream($stream, $entryName, \PhpZip\Constants\ZipCompressionMethod::STORED); // No compression
$zipFile->addFromStream($stream, $entryName, \PhpZip\Constants\ZipCompressionMethod::DEFLATED); // Deflate compression
$zipFile->addFromStream($stream, $entryName, \PhpZip\Constants\ZipCompressionMethod::BZIP2); // BZIP2 compression
```
<a name="Documentation-ZipFile-addEmptyDir"></a> **ZipFile::addEmptyDir** - add a new directory.
```php
$zipFile = new \PhpZip\ZipFile();
// $path = "path/to/";
$zipFile->addEmptyDir($path);
// or
$zipFile[$path] = null;
```
<a name="Documentation-ZipFile-addAll"></a> **ZipFile::addAll** - adds all entries from an array.
```php
$entries = [
    'file.txt' => 'file contents', // add an entry from the string contents
    'empty dir/' => null, // add empty directory
    'path/to/file.jpg' => fopen('..../filename', 'rb'), // add an entry from the stream
    'path/to/file.dat' => new \SplFileInfo('..../filename'), // add an entry from the file
];

$zipFile = new \PhpZip\ZipFile();
$zipFile->addAll($entries);
```
<a name="Documentation-ZipFile-addDir"></a> **ZipFile::addDir** - adds files to the archive from the directory on the specified path without subdirectories.
```php
$zipFile = new \PhpZip\ZipFile();
$zipFile->addDir($dirName);

// you can specify the path in the archive to which you want to put entries
$localPath = 'to/path/';
$zipFile->addDir($dirName, $localPath);

// you can specify a compression method
$zipFile->addDir($dirName, $localPath, \PhpZip\Constants\ZipCompressionMethod::STORED); // No compression
$zipFile->addDir($dirName, $localPath, \PhpZip\Constants\ZipCompressionMethod::DEFLATED); // Deflate compression
$zipFile->addDir($dirName, $localPath, \PhpZip\Constants\ZipCompressionMethod::BZIP2); // BZIP2 compression
```
<a name="Documentation-ZipFile-addDirRecursive"></a> **ZipFile::addDirRecursive** - adds files to the archive from the directory on the specified path with subdirectories.
```php
$zipFile = new \PhpZip\ZipFile();
$zipFile->addDirRecursive($dirName);

// you can specify the path in the archive to which you want to put entries
$localPath = 'to/path/';
$zipFile->addDirRecursive($dirName, $localPath);

// you can specify a compression method
$zipFile->addDirRecursive($dirName, $localPath, \PhpZip\Constants\ZipCompressionMethod::STORED); // No compression
$zipFile->addDirRecursive($dirName, $localPath, \PhpZip\Constants\ZipCompressionMethod::DEFLATED); // Deflate compression
$zipFile->addDirRecursive($dirName, $localPath, \PhpZip\Constants\ZipCompressionMethod::BZIP2); // BZIP2 compression
```
<a name="Documentation-ZipFile-addFilesFromIterator"></a> **ZipFile::addFilesFromIterator** - adds files from the iterator of directories.
```php
// $directoryIterator = new \DirectoryIterator($dir); // without subdirectories
// $directoryIterator = new \RecursiveDirectoryIterator($dir); // with subdirectories
$zipFile = new \PhpZip\ZipFile();
$zipFile->addFilesFromIterator($directoryIterator);

// you can specify the path in the archive to which you want to put entries
$localPath = 'to/path/';
$zipFile->addFilesFromIterator($directoryIterator, $localPath);
// or
$zipFile[$localPath] = $directoryIterator;

// you can specify a compression method
$zipFile->addFilesFromIterator($directoryIterator, $localPath, \PhpZip\Constants\ZipCompressionMethod::STORED); // No compression
$zipFile->addFilesFromIterator($directoryIterator, $localPath, \PhpZip\Constants\ZipCompressionMethod::DEFLATED); // Deflate compression
$zipFile->addFilesFromIterator($directoryIterator, $localPath, \PhpZip\Constants\ZipCompressionMethod::BZIP2); // BZIP2 compression
```
Example with some files ignoring:
```php
$ignoreFiles = [
    'file_ignore.txt', 
    'dir_ignore/sub dir ignore/'
];

// $directoryIterator = new \DirectoryIterator($dir); // without subdirectories
// $directoryIterator = new \RecursiveDirectoryIterator($dir); // with subdirectories
// use \PhpZip\Util\Iterator\IgnoreFilesFilterIterator for non-recursive search
 
$zipFile = new \PhpZip\ZipFile();
$ignoreIterator = new \PhpZip\Util\Iterator\IgnoreFilesRecursiveFilterIterator(
    $directoryIterator, 
    $ignoreFiles
);

$zipFile->addFilesFromIterator($ignoreIterator);
```
<a name="Documentation-ZipFile-addFilesFromGlob"></a> **ZipFile::addFilesFromGlob** - adds files from a directory by [glob pattern](https://en.wikipedia.org/wiki/Glob_(programming)) without subdirectories.
```php
$globPattern = '**.{jpg,jpeg,png,gif}'; // example glob pattern -> add all .jpg, .jpeg, .png and .gif files

$zipFile = new \PhpZip\ZipFile();
$zipFile->addFilesFromGlob($dir, $globPattern);

// you can specify the path in the archive to which you want to put entries
$localPath = 'to/path/';
$zipFile->addFilesFromGlob($dir, $globPattern, $localPath);

// you can specify a compression method
$zipFile->addFilesFromGlob($dir, $globPattern, $localPath, \PhpZip\Constants\ZipCompressionMethod::STORED); // No compression
$zipFile->addFilesFromGlob($dir, $globPattern, $localPath, \PhpZip\Constants\ZipCompressionMethod::DEFLATED); // Deflate compression
$zipFile->addFilesFromGlob($dir, $globPattern, $localPath, \PhpZip\Constants\ZipCompressionMethod::BZIP2); // BZIP2 compression
```
<a name="Documentation-ZipFile-addFilesFromGlobRecursive"></a> **ZipFile::addFilesFromGlobRecursive** - adds files from a directory by [glob pattern](https://en.wikipedia.org/wiki/Glob_(programming)) with subdirectories.
```php
$globPattern = '**.{jpg,jpeg,png,gif}'; // example glob pattern -> add all .jpg, .jpeg, .png and .gif files

$zipFile = new \PhpZip\ZipFile();
$zipFile->addFilesFromGlobRecursive($dir, $globPattern);

// you can specify the path in the archive to which you want to put entries
$localPath = 'to/path/';
$zipFile->addFilesFromGlobRecursive($dir, $globPattern, $localPath);

// you can specify a compression method
$zipFile->addFilesFromGlobRecursive($dir, $globPattern, $localPath, \PhpZip\Constants\ZipCompressionMethod::STORED); // No compression
$zipFile->addFilesFromGlobRecursive($dir, $globPattern, $localPath, \PhpZip\Constants\ZipCompressionMethod::DEFLATED); // Deflate compression
$zipFile->addFilesFromGlobRecursive($dir, $globPattern, $localPath, \PhpZip\Constants\ZipCompressionMethod::BZIP2); // BZIP2 compression
```
<a name="Documentation-ZipFile-addFilesFromRegex"></a> **ZipFile::addFilesFromRegex** - adds files from a directory by [PCRE pattern](https://en.wikipedia.org/wiki/Regular_expression) without subdirectories.
```php
$regexPattern = '/\.(jpe?g|png|gif)$/si'; // example regex pattern -> add all .jpg, .jpeg, .png and .gif files

$zipFile = new \PhpZip\ZipFile();
$zipFile->addFilesFromRegex($dir, $regexPattern);

// you can specify the path in the archive to which you want to put entries
$localPath = 'to/path/';
$zipFile->addFilesFromRegex($dir, $regexPattern, $localPath);

// you can specify a compression method
$zipFile->addFilesFromRegex($dir, $regexPattern, $localPath, \PhpZip\Constants\ZipCompressionMethod::STORED); // No compression
$zipFile->addFilesFromRegex($dir, $regexPattern, $localPath, \PhpZip\Constants\ZipCompressionMethod::DEFLATED); // Deflate compression
$zipFile->addFilesFromRegex($dir, $regexPattern, $localPath, \PhpZip\Constants\ZipCompressionMethod::BZIP2); // BZIP2 compression
```
<a name="Documentation-ZipFile-addFilesFromRegexRecursive"></a> **ZipFile::addFilesFromRegexRecursive** - adds files from a directory by [PCRE pattern](https://en.wikipedia.org/wiki/Regular_expression) with subdirectories.
```php
$regexPattern = '/\.(jpe?g|png|gif)$/si'; // example regex pattern -> add all .jpg, .jpeg, .png and .gif files


$zipFile->addFilesFromRegexRecursive($dir, $regexPattern);

// you can specify the path in the archive to which you want to put entries
$localPath = 'to/path/';
$zipFile->addFilesFromRegexRecursive($dir, $regexPattern, $localPath);

// you can specify a compression method
$zipFile->addFilesFromRegexRecursive($dir, $regexPattern, $localPath, \PhpZip\Constants\ZipCompressionMethod::STORED); // No compression
$zipFile->addFilesFromRegexRecursive($dir, $regexPattern, $localPath, \PhpZip\Constants\ZipCompressionMethod::DEFLATED); // Deflate compression
$zipFile->addFilesFromRegexRecursive($dir, $regexPattern, $localPath, \PhpZip\Constants\ZipCompressionMethod::BZIP2); // BZIP2 compression
```
#### <a name="Documentation-Remove-Zip-Entries"></a> Deleting entries from the archive
<a name="Documentation-ZipFile-deleteFromName"></a> **ZipFile::deleteFromName** - deletes an entry in the archive using its name.
```php
$zipFile = new \PhpZip\ZipFile();
$zipFile->deleteFromName($entryName);
```
<a name="Documentation-ZipFile-deleteFromGlob"></a> **ZipFile::deleteFromGlob** - deletes a entries in the archive using [glob pattern](https://en.wikipedia.org/wiki/Glob_(programming)).
```php
$globPattern = '**.{jpg,jpeg,png,gif}'; // example glob pattern -> delete all .jpg, .jpeg, .png and .gif files

$zipFile = new \PhpZip\ZipFile();
$zipFile->deleteFromGlob($globPattern);
```
<a name="Documentation-ZipFile-deleteFromRegex"></a> **ZipFile::deleteFromRegex** - deletes a entries in the archive using [PCRE pattern](https://en.wikipedia.org/wiki/Regular_expression).
```php
$regexPattern = '/\.(jpe?g|png|gif)$/si'; // example regex pattern -> delete all .jpg, .jpeg, .png and .gif files

$zipFile = new \PhpZip\ZipFile();
$zipFile->deleteFromRegex($regexPattern);
```
<a name="Documentation-ZipFile-deleteAll"></a> **ZipFile::deleteAll** - deletes all entries in the ZIP archive.
```php
$zipFile = new \PhpZip\ZipFile();
$zipFile->deleteAll();
```
#### <a name="Documentation-Entries"></a> Working with entries and archive
<a name="Documentation-ZipFile-rename"></a> **ZipFile::rename** - renames an entry defined by its name.
```php
$zipFile = new \PhpZip\ZipFile();
$zipFile->rename($oldName, $newName);
```
<a name="Documentation-ZipFile-setCompressionLevel"></a> **ZipFile::setCompressionLevel** - set the compression level for all files in the archive.

> _Note that this method does not apply to entries that are added after this method is run._

By default, the compression level is 5 (`\PhpZip\Constants\ZipCompressionLevel::NORMAL`) or the compression level specified in the archive for Deflate compression.

The values range from 1 (`\PhpZip\Constants\ZipCompressionLevel::SUPER_FAST`) to 9 (`\PhpZip\Constants\ZipCompressionLevel::MAXIMUM`) are supported. The higher the number, the better and longer the compression.
```php
$zipFile = new \PhpZip\ZipFile();
$zipFile->setCompressionLevel(\PhpZip\Constants\ZipCompressionLevel::MAXIMUM);
```
<a name="Documentation-ZipFile-setCompressionLevelEntry"></a> **ZipFile::setCompressionLevelEntry** - sets the compression level for the entry by its name.

The values range from 1 (`\PhpZip\Constants\ZipCompressionLevel::SUPER_FAST`) to 9 (`\PhpZip\Constants\ZipCompressionLevel::MAXIMUM`) are supported. The higher the number, the better and longer the compression.
```php
$zipFile = new \PhpZip\ZipFile();
$zipFile->setCompressionLevelEntry($entryName, \PhpZip\Constants\ZipCompressionLevel::FAST);
```
<a name="Documentation-ZipFile-setCompressionMethodEntry"></a> **ZipFile::setCompressionMethodEntry** - sets the compression method for the entry by its name.

The following compression methods are available:
- `\PhpZip\Constants\ZipCompressionMethod::STORED` - No compression
- `\PhpZip\Constants\ZipCompressionMethod::DEFLATED` - Deflate compression
- `\PhpZip\Constants\ZipCompressionMethod::BZIP2` - Bzip2 compression with the extension `ext-bz2`
```php
$zipFile = new \PhpZip\ZipFile();
$zipFile->setCompressionMethodEntry($entryName, \PhpZip\Constants\ZipCompressionMethod::DEFLATED);
```
<a name="Documentation-ZipFile-setArchiveComment"></a> **ZipFile::setArchiveComment** - set the comment of a ZIP archive.
```php
$zipFile = new \PhpZip\ZipFile();
$zipFile->setArchiveComment($commentArchive);
```
<a name="Documentation-ZipFile-setEntryComment"></a> **ZipFile::setEntryComment** - set the comment of an entry defined by its name.
```php
$zipFile = new \PhpZip\ZipFile();
$zipFile->setEntryComment($entryName, $comment);
```
<a name="Documentation-ZipFile-matcher"></a> **ZipFile::matcher** - selecting entries in the archive to perform operations on them.
```php
$zipFile = new \PhpZip\ZipFile();
$matcher = $zipFile->matcher();
```
Selecting files from the archive one at a time:
```php
$matcher
    ->add('entry name')
    ->add('another entry');
```
Select multiple files in the archive:
```php
$matcher->add([
    'entry name',
    'another entry name',
    'path/'
]);
```
Selecting files by regular expression:
```php
$matcher->match('~\.jpe?g$~i');
```
Select all files in the archive:
```php
$matcher->all();
```
count() - gets the number of selected entries:
```php
$count = count($matcher);
// or
$count = $matcher->count();
```
getMatches() - returns a list of selected entries:
```php
$entries = $matcher->getMatches();
// example array contents: ['entry name', 'another entry name'];
```
invoke() - invoke a callable function on selected entries:
```php
// example
$matcher->invoke(static function($entryName) use($zipFile) {
    $newName = preg_replace('~\.(jpe?g)$~i', '.no_optimize.$1', $entryName);
    $zipFile->rename($entryName, $newName);
});
```
Functions for working on the selected entries:
```php
$matcher->delete(); // remove selected entries from a ZIP archive
$matcher->setPassword($password); // sets a new password for the selected entries
$matcher->setPassword($password, $encryptionMethod); // sets a new password and encryption method to selected entries
$matcher->setEncryptionMethod($encryptionMethod); // sets the encryption method to the selected entries
$matcher->disableEncryption(); // disables encryption for selected entries
```
#### <a name="Documentation-Password"></a> Working with passwords

Implemented support for encryption methods:
- `\PhpZip\Constants\ZipEncryptionMethod::PKWARE` - Traditional PKWARE encryption (legacy)
- `\PhpZip\Constants\ZipEncryptionMethod::WINZIP_AES_256` - WinZip AES encryption 256 bit (recommended)
- `\PhpZip\Constants\ZipEncryptionMethod::WINZIP_AES_192` - WinZip AES encryption 192 bit
- `\PhpZip\Constants\ZipEncryptionMethod::WINZIP_AES_128` - WinZip AES encryption 128 bit

<a name="Documentation-ZipFile-setReadPassword"></a> **ZipFile::setReadPassword** - set the password for the open archive.

> _Setting a password is not required for adding new entries or deleting existing ones, but if you want to extract the content or change the method / compression level, the encryption method, or change the password, in this case the password must be specified._
```php
$zipFile->setReadPassword($password);
```
<a name="Documentation-ZipFile-setReadPasswordEntry"></a> **ZipFile::setReadPasswordEntry** - gets a password for reading of an entry defined by its name.
```php
$zipFile->setReadPasswordEntry($entryName, $password);
```
<a name="Documentation-ZipFile-setPassword"></a> **ZipFile::setPassword** - sets a new password for all files in the archive.

> _Note that this method does not apply to entries that are added after this method is run._
```php
$zipFile->setPassword($password);
```
You can set the encryption method:
```php
$encryptionMethod = \PhpZip\Constants\ZipEncryptionMethod::WINZIP_AES_256;
$zipFile->setPassword($password, $encryptionMethod);
```
<a name="Documentation-ZipFile-setPasswordEntry"></a> **ZipFile::setPasswordEntry** - sets a new password of an entry defined by its name.
```php
$zipFile->setPasswordEntry($entryName, $password);
```
You can set the encryption method:
```php
$encryptionMethod = \PhpZip\Constants\ZipEncryptionMethod::WINZIP_AES_256;
$zipFile->setPasswordEntry($entryName, $password, $encryptionMethod);
```
<a name="Documentation-ZipFile-disableEncryption"></a> **ZipFile::disableEncryption** - disable encryption for all entries that are already in the archive.

> _Note that this method does not apply to entries that are added after this method is run._
```php
$zipFile->disableEncryption();
```
<a name="Documentation-ZipFile-disableEncryptionEntry"></a> **ZipFile::disableEncryptionEntry** - disable encryption of an entry defined by its name.
```php
$zipFile->disableEncryptionEntry($entryName);
```
#### <a name="Documentation-ZipAlign-Usage"></a> zipalign
<a name="Documentation-ZipFile-setZipAlign"></a> **ZipFile::setZipAlign** - sets the alignment of the archive to optimize APK files (Android packages).

This method adds padding to unencrypted and not compressed entries, to optimize memory consumption in the Android system. It is recommended to use for `APK` files. The file may grow slightly.

This method is an alternative to executing the `zipalign -f -v 4 filename.zip`.

More details can be found on the [link](https://developer.android.com/studio/command-line/zipalign.html).
```php
$zipFile->setZipAlign(4);
```
#### <a name="Documentation-Unchanged"></a> Undo changes
<a name="Documentation-ZipFile-unchangeAll"></a> **ZipFile::unchangeAll** - undo all changes done in the archive.
```php
$zipFile->unchangeAll();
```
<a name="Documentation-ZipFile-unchangeArchiveComment"></a> **ZipFile::unchangeArchiveComment** - undo changes to the archive comment.
```php
$zipFile->unchangeArchiveComment();
```
<a name="Documentation-ZipFile-unchangeEntry"></a> **ZipFile::unchangeEntry** - undo changes of an entry defined by its name.
```php
$zipFile->unchangeEntry($entryName);
```
#### <a name="Documentation-Save-Or-Output-Entries"></a> Saving a file or output to a browser
<a name="Documentation-ZipFile-saveAsFile"></a> **ZipFile::saveAsFile** - saves the archive to a file.
```php
$zipFile->saveAsFile($filename);
```
<a name="Documentation-ZipFile-saveAsStream"></a> **ZipFile::saveAsStream** - writes the archive to the stream.
```php
// $fp = fopen($filename, 'w+b');

$zipFile->saveAsStream($fp);
```
<a name="Documentation-ZipFile-outputAsString"></a> **ZipFile::outputAsString** - outputs a ZIP-archive as string.
```php
$rawZipArchiveBytes = $zipFile->outputAsString();
```
<a name="Documentation-ZipFile-outputAsAttachment"></a> **ZipFile::outputAsAttachment** - outputs a ZIP-archive to the browser.
```php
$zipFile->outputAsAttachment($outputFilename);
```
You can set the Mime-Type:
```php
$mimeType = 'application/zip';
$zipFile->outputAsAttachment($outputFilename, $mimeType);
```
<a name="Documentation-ZipFile-outputAsResponse"></a> **ZipFile::outputAsResponse** - outputs a ZIP-archive as [PSR-7 Response](http://www.php-fig.org/psr/psr-7/).

The output method can be used in any PSR-7 compatible framework. 
```php
// $response = ....; // instance Psr\Http\Message\ResponseInterface
$zipFile->outputAsResponse($response, $outputFilename);
```
You can set the Mime-Type:
```php
$mimeType = 'application/zip';
$zipFile->outputAsResponse($response, $outputFilename, $mimeType);
```
<a name="Documentation-ZipFile-rewrite"></a> **ZipFile::rewrite** - save changes and re-open the changed archive.
```php
$zipFile->rewrite();
```
#### <a name="Documentation-Close-Zip-Archive"></a> Closing the archive
<a name="Documentation-ZipFile-close"></a> **ZipFile::close** - close the archive.
```php
$zipFile->close();
```
### <a name="Running-Tests"></a> Running the tests
Install the dependencies for the development:
```bash
composer install --dev
```
Run the tests:
```bash
vendor/bin/phpunit
```
### <a name="Changelog"></a> Changelog
Changes are documented in the [releases page](https://github.com/Ne-Lexa/php-zip/releases).

### <a name="Upgrade"></a> Upgrade
#### <a name="Upgrade-v2-to-v3"></a> Upgrade version 2 to version 3.0
Update the major version in the file `composer.json` to `^3.0`.
```json
{
    "require": {
        "nelexa/zip": "^3.0"
    }
}
```
Then install updates using `Composer`:
```bash
composer update nelexa/zip
```
Update your code to work with the new version:
- Class `ZipOutputFile` merged to `ZipFile` and removed.
  + `new \PhpZip\ZipOutputFile()` to `new \PhpZip\ZipFile()`
- Static initialization methods are now not static.
  + `\PhpZip\ZipFile::openFromFile($filename);` to `(new \PhpZip\ZipFile())->openFile($filename);`
  + `\PhpZip\ZipOutputFile::openFromFile($filename);` to `(new \PhpZip\ZipFile())->openFile($filename);`
  + `\PhpZip\ZipFile::openFromString($contents);` to `(new \PhpZip\ZipFile())->openFromString($contents);`
  + `\PhpZip\ZipFile::openFromStream($stream);` to `(new \PhpZip\ZipFile())->openFromStream($stream);`
  + `\PhpZip\ZipOutputFile::create()` to `new \PhpZip\ZipFile()`
  + `\PhpZip\ZipOutputFile::openFromZipFile(\PhpZip\ZipFile $zipFile)` &gt; `(new \PhpZip\ZipFile())->openFile($filename);`
- Rename methods:
  + `addFromFile` to `addFile`
  + `setLevel` to `setCompressionLevel`
  + `ZipFile::setPassword` to `ZipFile::withReadPassword`
  + `ZipOutputFile::setPassword` to `ZipFile::withNewPassword`
  + `ZipOutputFile::disableEncryptionAllEntries` to `ZipFile::withoutPassword`
  + `ZipOutputFile::setComment` to `ZipFile::setArchiveComment`
  + `ZipFile::getComment` to `ZipFile::getArchiveComment`
- Changed signature for methods `addDir`, `addFilesFromGlob`, `addFilesFromRegex`.
- Remove methods:
  + `getLevel`
  + `setCompressionMethod`
  + `setEntryPassword`


