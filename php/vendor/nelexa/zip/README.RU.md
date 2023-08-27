`PhpZip`
========
`PhpZip` - php библиотека для продвинутой работы с ZIP-архивами.

[![Build Status](https://travis-ci.org/Ne-Lexa/php-zip.svg?branch=master)](https://travis-ci.org/Ne-Lexa/php-zip)
[![Code Coverage](https://scrutinizer-ci.com/g/Ne-Lexa/php-zip/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/Ne-Lexa/php-zip/?branch=master)
[![Latest Stable Version](https://poser.pugx.org/nelexa/zip/v/stable)](https://packagist.org/packages/nelexa/zip)
[![Total Downloads](https://poser.pugx.org/nelexa/zip/downloads)](https://packagist.org/packages/nelexa/zip)
[![Minimum PHP Version](http://img.shields.io/badge/php-%3E%3D%205.5-8892BF.svg)](https://php.net/)
[![License](https://poser.pugx.org/nelexa/zip/license)](https://packagist.org/packages/nelexa/zip)

[English Documentation](README.md)

Содержание
----------
- [Функционал](#Features)
- [Требования](#Requirements)
- [Установка](#Installation)
- [Примеры](#Examples)
- [Глоссарий](#Glossary)
- [Документация](#Documentation)
  + [Обзор методов класса `\PhpZip\ZipFile`](#Documentation-Overview)
  + [Создание/Открытие ZIP-архива](#Documentation-Open-Zip-Archive)
  + [Чтение записей из архива](#Documentation-Open-Zip-Entries)
  + [Перебор записей/Итератор](#Documentation-Zip-Iterate)
  + [Получение информации о записях](#Documentation-Zip-Info)
  + [Добавление записей в архив](#Documentation-Add-Zip-Entries)
  + [Удаление записей из архива](#Documentation-Remove-Zip-Entries)
  + [Работа с записями и с архивом](#Documentation-Entries)
  + [Работа с паролями](#Documentation-Password)
  + [zipalign - выравнивание архива для оптимизации Android пакетов (APK)](#Documentation-ZipAlign-Usage)
  + [Отмена изменений](#Documentation-Unchanged)
  + [Сохранение файла или вывод в браузер](#Documentation-Save-Or-Output-Entries)
  + [Закрытие архива](#Documentation-Close-Zip-Archive)
- [Запуск тестов](#Running-Tests)
- [История изменений](#Changelog)
- [Обновление версий](#Upgrade)
  + [Обновление с версии 2 до версии 3.0](#Upgrade-v2-to-v3)

### <a name="Features"></a> Функционал
- Открытие и разархивирование ZIP-архивов.
- Создание ZIP-архивов.
- Модификация ZIP-архивов.
- Чистый php (не требуется расширение `php-zip` и класс `\ZipArchive`).
- Поддерживается сохранение архива в файл, вывод архива в браузер или вывод в виде строки, без сохранения в файл.
- Поддерживаются комментарии архива и комментарии отдельных записей.
- Получение подробной информации о каждой записи в архиве.
- Поддерживаются только следующие методы сжатия:
  + Без сжатия (Stored).
  + Deflate сжатие.
  + BZIP2 сжатие при наличии расширения `php-bz2`.
- Поддержка `ZIP64` (размер файла более 4 GB или количество записей в архиве более 65535).
- Встроенная поддержка выравнивания архива для оптимизации Android пакетов (APK) [`zipalign`](https://developer.android.com/studio/command-line/zipalign.html).
- Работа с паролями для PHP 5.5
  > **Внимание!**
  >
  > Для 32-bit систем, в данный момент не поддерживается метод шифрование `Traditional PKWARE Encryption (ZipCrypto)`. 
  > Используйте метод шифрования `WinZIP AES Encryption`, когда это возможно.
  + Установка пароля для чтения архива глобально или для некоторых записей.
  + Изменение пароля архива, в том числе и для отдельных записей.
  + Удаление пароля архива глобально или для отдельных записей.
  + Установка пароля и/или метода шифрования, как для всех, так и для отдельных записей в архиве.
  + Установка разных паролей и методов шифрования для разных записей.
  + Удаление пароля для всех или для некоторых записей.
  + Поддержка методов шифрования `Traditional PKWARE Encryption (ZipCrypto)` и `WinZIP AES Encryption (128, 192 или 256 bit)`.
  + Установка метода шифрования для всех или для отдельных записей в архиве.

### <a name="Requirements"></a> Требования
- `PHP` >= 5.5 (предпочтительно 64-bit).
- Опционально php-расширение `bzip2` для поддержки BZIP2 компрессии.
- Опционально php-расширение `openssl` или `mcrypt` для `WinZip Aes Encryption` шифрования.

### <a name="Installation"></a> Установка
`composer require nelexa/zip`

Последняя стабильная версия: [![Latest Stable Version](https://poser.pugx.org/nelexa/zip/v/stable)](https://packagist.org/packages/nelexa/zip)

### <a name="Examples"></a> Примеры
```php
// создание нового архива
$zipFile = new \PhpZip\ZipFile();
try{
    $zipFile
        ->addFromString('zip/entry/filename', "Is file content") // добавить запись из строки
        ->addFile('/path/to/file', 'data/tofile') // добавить запись из файла
        ->addDir(__DIR__, 'to/path/') // добавить файлы из директории
        ->saveAsFile($outputFilename) // сохранить архив в файл
        ->close(); // закрыть архив
            
    // открытие архива, извлечение файлов, удаление файлов, добавление файлов, установка пароля и вывод архива в браузер.
    $zipFile
        ->openFile($outputFilename) // открыть архив из файла
        ->extractTo($outputDirExtract) // извлечь файлы в заданную директорию
        ->deleteFromRegex('~^\.~') // удалить все скрытые (Unix) файлы
        ->addFromString('dir/file.txt', 'Test file') // добавить новую запись из строки
        ->setPassword('password') // установить пароль на все записи
        ->outputAsAttachment('library.jar'); // вывести в браузер без сохранения в файл
}
catch(\PhpZip\Exception\ZipException $e){
    // обработка исключения
}
finally{
    $zipFile->close();
}
```
Другие примеры можно посмотреть в папке `tests/`.

### <a name="Glossary"></a> Глоссарий
**Запись в ZIP-архиве (Zip Entry)** - файл или папка в ZIP-архиве. У каждой записи в архиве есть определённые свойства, например: имя файла, метод сжатия, метод шифрования, размер файла до сжатия, размер файла после сжатия, CRC32 и другие.

### <a name="Documentation"></a> Документация
#### <a name="Documentation-Overview"></a> Обзор методов класса `\PhpZip\ZipFile`
- [ZipFile::__construct](#Documentation-ZipFile-__construct) - инициализацирует ZIP-архив.
- [ZipFile::addAll](#Documentation-ZipFile-addAll) - добавляет все записи из массива.
- [ZipFile::addDir](#Documentation-ZipFile-addDir) - добавляет файлы из директории по указанному пути без вложенных директорий.
- [ZipFile::addDirRecursive](#Documentation-ZipFile-addDirRecursive) - добавляет файлы из директории по указанному пути c вложенными директориями.
- [ZipFile::addEmptyDir](#Documentation-ZipFile-addEmptyDir) - добавляет в ZIP-архив новую директорию.
- [ZipFile::addFile](#Documentation-ZipFile-addFile) - добавляет в ZIP-архив файл по указанному пути.
- [ZipFile::addSplFile](#Documentation-ZipFile-addSplFile) - добавляет объект `\SplFileInfo` в zip-архив.
- [ZipFile::addFromFinder](#Documentation-ZipFile-addFromFinder) - добавляет файлы из `Symfony\Component\Finder\Finder` в zip архив.
- [ZipFile::addFilesFromIterator](#Documentation-ZipFile-addFilesFromIterator) - добавляет файлы из итератора директорий.
- [ZipFile::addFilesFromGlob](#Documentation-ZipFile-addFilesFromGlob) - добавляет файлы из директории в соответствии с glob шаблоном без вложенных директорий.
- [ZipFile::addFilesFromGlobRecursive](#Documentation-ZipFile-addFilesFromGlobRecursive) - добавляет файлы из директории в соответствии с glob шаблоном c вложенными директориями.
- [ZipFile::addFilesFromRegex](#Documentation-ZipFile-addFilesFromRegex) - добавляет файлы из директории в соответствии с регулярным выражением без вложенных директорий.
- [ZipFile::addFilesFromRegexRecursive](#Documentation-ZipFile-addFilesFromRegexRecursive) - добавляет файлы из директории в соответствии с регулярным выражением c вложенными директориями.
- [ZipFile::addFromStream](#Documentation-ZipFile-addFromStream) - добавляет в ZIP-архив запись из потока.
- [ZipFile::addFromString](#Documentation-ZipFile-addFromString) - добавляет файл в ZIP-архив, используя его содержимое в виде строки.
- [ZipFile::close](#Documentation-ZipFile-close) - закрывает ZIP-архив.
- [ZipFile::count](#Documentation-ZipFile-count) - возвращает количество записей в архиве.
- [ZipFile::deleteFromName](#Documentation-ZipFile-deleteFromName) - удаляет запись по имени.
- [ZipFile::deleteFromGlob](#Documentation-ZipFile-deleteFromGlob) - удаляет записи в соответствии с glob шаблоном.
- [ZipFile::deleteFromRegex](#Documentation-ZipFile-deleteFromRegex) - удаляет записи в соответствии с регулярным выражением.
- [ZipFile::deleteAll](#Documentation-ZipFile-deleteAll) - удаляет все записи в ZIP-архиве.
- [ZipFile::disableEncryption](#Documentation-ZipFile-disableEncryption) - отключает шифрования всех записей, находящихся в архиве.
- [ZipFile::disableEncryptionEntry](#Documentation-ZipFile-disableEncryptionEntry) - отключает шифрование записи по её имени.
- [ZipFile::extractTo](#Documentation-ZipFile-extractTo) - извлекает содержимое архива в заданную директорию.
- [ZipFile::getAllInfo](#Documentation-ZipFile-getAllInfo) - возвращает подробную информацию обо всех записях в архиве.
- [ZipFile::getArchiveComment](#Documentation-ZipFile-getArchiveComment) - возвращает комментарий ZIP-архива.
- [ZipFile::getEntryComment](#Documentation-ZipFile-getEntryComment) - возвращает комментарий к записи, используя её имя.
- [ZipFile::getEntryContent](#Documentation-ZipFile-getEntryContent) - возвращает содержимое записи.
- [ZipFile::getEntryInfo](#Documentation-ZipFile-getEntryInfo) - возвращает подробную информацию о записи в архиве.
- [ZipFile::getListFiles](#Documentation-ZipFile-getListFiles) - возвращает список файлов архива.
- [ZipFile::hasEntry](#Documentation-ZipFile-hasEntry) - проверяет, присутствует ли запись в архиве.
- [ZipFile::isDirectory](#Documentation-ZipFile-isDirectory) - проверяет, является ли запись в архиве директорией.
- [ZipFile::matcher](#Documentation-ZipFile-matcher) - выборка записей в архиве для проведения операций над выбранными записями.
- [ZipFile::openFile](#Documentation-ZipFile-openFile) - открывает ZIP-архив из файла.
- [ZipFile::openFromString](#Documentation-ZipFile-openFromString) - открывает ZIP-архив из строки.
- [ZipFile::openFromStream](#Documentation-ZipFile-openFromStream) - открывает ZIP-архив из потока.
- [ZipFile::outputAsAttachment](#Documentation-ZipFile-outputAsAttachment) - выводит ZIP-архив в браузер.
- [ZipFile::outputAsResponse](#Documentation-ZipFile-outputAsResponse) - выводит ZIP-архив, как Response PSR-7.
- [ZipFile::outputAsString](#Documentation-ZipFile-outputAsString) - выводит ZIP-архив в виде строки.
- [ZipFile::rename](#Documentation-ZipFile-rename) - переименовывает запись по имени.
- [ZipFile::rewrite](#Documentation-ZipFile-rewrite) - сохраняет изменения и заново открывает изменившийся архив.
- [ZipFile::saveAsFile](#Documentation-ZipFile-saveAsFile) - сохраняет архив в файл.
- [ZipFile::saveAsStream](#Documentation-ZipFile-saveAsStream) - записывает архив в поток.
- [ZipFile::setArchiveComment](#Documentation-ZipFile-setArchiveComment) - устанавливает комментарий к ZIP-архиву.
- [ZipFile::setCompressionLevel](#Documentation-ZipFile-setCompressionLevel) - устанавливает уровень сжатия для всех файлов, находящихся в архиве.
- [ZipFile::setCompressionLevelEntry](#Documentation-ZipFile-setCompressionLevelEntry) - устанавливает уровень сжатия для определённой записи в архиве.
- [ZipFile::setCompressionMethodEntry](#Documentation-ZipFile-setCompressionMethodEntry) - устанавливает метод сжатия для определённой записи в архиве.
- [ZipFile::setEntryComment](#Documentation-ZipFile-setEntryComment) - устанавливает комментарий к записи, используя её имя.
- [ZipFile::setReadPassword](#Documentation-ZipFile-setReadPassword) - устанавливает пароль на чтение открытого запароленного архива для всех зашифрованных записей.
- [ZipFile::setReadPasswordEntry](#Documentation-ZipFile-setReadPasswordEntry) - устанавливает пароль на чтение конкретной зашифрованной записи открытого запароленного архива.
- ~~ZipFile::withNewPassword~~ - устаревший метод (**deprecated**) используйте метод [ZipFile::setPassword](#Documentation-ZipFile-setPassword).
- [ZipFile::setPassword](#Documentation-ZipFile-setPassword) - устанавливает новый пароль для всех файлов, находящихся в архиве.
- [ZipFile::setPasswordEntry](#Documentation-ZipFile-setPasswordEntry) - устанавливает новый пароль для конкретного файла.
- [ZipFile::setZipAlign](#Documentation-ZipFile-setZipAlign) - устанавливает выравнивание архива для оптимизации APK файлов (Android packages).
- [ZipFile::unchangeAll](#Documentation-ZipFile-unchangeAll) - отменяет все изменения, сделанные в архиве.
- [ZipFile::unchangeArchiveComment](#Documentation-ZipFile-unchangeArchiveComment) - отменяет изменения в комментарии к архиву.
- [ZipFile::unchangeEntry](#Documentation-ZipFile-unchangeEntry) - отменяет изменения для конкретной записи архива.
- ~~ZipFile::withoutPassword~~ - устаревший метод (**deprecated**) используйте метод [ZipFile::disableEncryption](#Documentation-ZipFile-disableEncryption).
- ~~ZipFile::withReadPassword~~ - устаревший метод (**deprecated**) используйте метод [ZipFile::setReadPassword](#Documentation-ZipFile-setReadPassword).

#### <a name="Documentation-Open-Zip-Archive"></a> Создание/Открытие ZIP-архива
<a name="Documentation-ZipFile-__construct"></a>**ZipFile::__construct** - Инициализацирует ZIP-архив.
```php
$zipFile = new \PhpZip\ZipFile();
```
<a name="Documentation-ZipFile-openFile"></a> **ZipFile::openFile** - открывает ZIP-архив из файла.
```php
$zipFile = new \PhpZip\ZipFile();
$zipFile->openFile('file.zip');
```
<a name="Documentation-ZipFile-openFromString"></a> **ZipFile::openFromString** - открывает ZIP-архив из строки.
```php
$zipFile = new \PhpZip\ZipFile();
$zipFile->openFromString($stringContents);
```
<a name="Documentation-ZipFile-openFromStream"></a> **ZipFile::openFromStream** - открывает ZIP-архив из потока.
```php
$stream = fopen('file.zip', 'rb');

$zipFile = new \PhpZip\ZipFile();
$zipFile->openFromStream($stream);
```
#### <a name="Documentation-Open-Zip-Entries"></a> Чтение записей из архива
<a name="Documentation-ZipFile-count"></a> **ZipFile::count** - возвращает количество записей в архиве.
```php
$zipFile = new \PhpZip\ZipFile();

$count = count($zipFile);
// или
$count = $zipFile->count();
```
<a name="Documentation-ZipFile-getListFiles"></a> **ZipFile::getListFiles** - возвращает список файлов архива.
```php
$zipFile = new \PhpZip\ZipFile();
$listFiles = $zipFile->getListFiles();

// Пример содержимого массива:
// array (
//   0 => 'info.txt',
//   1 => 'path/to/file.jpg',
//   2 => 'another path/',
// )
```
<a name="Documentation-ZipFile-getEntryContent"></a> **ZipFile::getEntryContent** - возвращает содержимое записи.
```php
// $entryName = 'path/to/example-entry-name.txt';
$zipFile = new \PhpZip\ZipFile();

$contents = $zipFile[$entryName];
// или
$contents = $zipFile->getEntryContents($entryName);
```
<a name="Documentation-ZipFile-hasEntry"></a> **ZipFile::hasEntry** - проверяет, присутствует ли запись в архиве.
```php
// $entryName = 'path/to/example-entry-name.txt';
$zipFile = new \PhpZip\ZipFile();

$hasEntry = isset($zipFile[$entryName]);
// или
$hasEntry = $zipFile->hasEntry($entryName);
```
<a name="Documentation-ZipFile-isDirectory"></a> **ZipFile::isDirectory** - проверяет, является ли запись в архиве директорией.
```php
// $entryName = 'path/to/';
$zipFile = new \PhpZip\ZipFile();

$isDirectory = $zipFile->isDirectory($entryName);
```
<a name="Documentation-ZipFile-extractTo"></a> **ZipFile::extractTo** - извлекает содержимое архива в заданную директорию.
Директория должна существовать.
```php
$zipFile = new \PhpZip\ZipFile();
$zipFile->extractTo($directory);
```
Можно извлечь только некоторые записи в заданную директорию.
Директория должна существовать.
```php
$extractOnlyFiles = [
    'filename1', 
    'filename2', 
    'dir/dir/dir/'
];
$zipFile = new \PhpZip\ZipFile();
$zipFile->extractTo($toDirectory, $extractOnlyFiles);
```
#### <a name="Documentation-Zip-Iterate"></a> Перебор записей/Итератор
`ZipFile` является итератором.
Можно перебрать все записи, через цикл `foreach`.
```php
foreach($zipFile as $entryName => $contents){
    echo "Файл: $entryName" . PHP_EOL;
    echo "Содержимое: $contents" . PHP_EOL;
    echo '-----------------------------' . PHP_EOL;
}
```
Можно использовать паттерн `Iterator`.
```php
$iterator = new \ArrayIterator($zipFile);
while ($iterator->valid())
{
    $entryName = $iterator->key();
    $contents = $iterator->current();

    echo "Файл: $entryName" . PHP_EOL;
    echo "Содержимое: $contents" . PHP_EOL;
    echo '-----------------------------' . PHP_EOL;

    $iterator->next();
}
```
#### <a name="Documentation-Zip-Info"></a> Получение информации о записях
<a name="Documentation-ZipFile-getArchiveComment"></a> **ZipFile::getArchiveComment** - возвращает комментарий ZIP-архива.
```php
$commentArchive = $zipFile->getArchiveComment();
```
<a name="Documentation-ZipFile-getEntryComment"></a> **ZipFile::getEntryComment** - возвращает комментарий к записи, используя её имя.
```php
$commentEntry = $zipFile->getEntryComment($entryName);
```
<a name="Documentation-ZipFile-getEntryInfo"></a> **ZipFile::getEntryInfo** - возвращает подробную информацию о записи в архиве.
```php
$zipFile = new \PhpZip\ZipFile();
$zipInfo = $zipFile->getEntryInfo('file.txt');
```
<a name="Documentation-ZipFile-getAllInfo"></a> **ZipFile::getAllInfo** - возвращает подробную информацию обо всех записях в архиве.
```php
$zipAllInfo = $zipFile->getAllInfo();
```
#### <a name="Documentation-Add-Zip-Entries"></a> Добавление записей в архив

Все методы добавления записей в ZIP-архив позволяют указать метод сжатия содержимого.

Доступны следующие методы сжатия:
- `\PhpZip\Constants\ZipCompressionMethod::STORED` - без сжатия
- `\PhpZip\Constants\ZipCompressionMethod::DEFLATED` - Deflate сжатие
- `\PhpZip\Constants\ZipCompressionMethod::BZIP2` - Bzip2 сжатие при наличии расширения `ext-bz2`

<a name="Documentation-ZipFile-addFile"></a> **ZipFile::addFile** - добавляет в ZIP-архив файл по указанному пути из файловой системы.
```php
$zipFile = new \PhpZip\ZipFile();
// $file = '...../file.ext'; 
$zipFile->addFile($file);

// можно указать имя записи в архиве (если null, то используется последний компонент из имени файла)
$zipFile->addFile($file, $entryName);

// можно указать метод сжатия
$zipFile->addFile($file, $entryName, \PhpZip\Constants\ZipCompressionMethod::STORED); // Без сжатия
$zipFile->addFile($file, $entryName, \PhpZip\Constants\ZipCompressionMethod::DEFLATED); // Deflate сжатие
$zipFile->addFile($file, $entryName, \PhpZip\Constants\ZipCompressionMethod::BZIP2); // BZIP2 сжатие
```
<a name="Documentation-ZipFile-addSplFile"></a>
**ZipFile::addSplFile"** - добавляет объект `\SplFileInfo` в zip-архив.
```php
// $file = '...../file.ext'; 
// $entryName = 'file2.ext'
$zipFile = new \PhpZip\ZipFile();

$splFile = new \SplFileInfo('README.md');

$zipFile->addSplFile($splFile);
$zipFile->addSplFile($splFile, $entryName);
// or
$zipFile[$entryName] = new \SplFileInfo($file);

// установить метод сжатия
$zipFile->addSplFile($splFile, $entryName, $options = [
    \PhpZip\Constants\ZipOptions::COMPRESSION_METHOD => \PhpZip\Constants\ZipCompressionMethod::DEFLATED,
]);
```
<a name="Documentation-ZipFile-addFromFinder"></a>
**ZipFile::addFromFinder"** - добавляет файлы из `Symfony\Component\Finder\Finder` в zip архив.
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
<a name="Documentation-ZipFile-addFromString"></a> **ZipFile::addFromString** - добавляет файл в ZIP-архив, используя его содержимое в виде строки.
```php
$zipFile = new \PhpZip\ZipFile();

$zipFile[$entryName] = $contents;
// или
$zipFile->addFromString($entryName, $contents);

// можно указать метод сжатия
$zipFile->addFromString($entryName, $contents, \PhpZip\Constants\ZipCompressionMethod::STORED); // Без сжатия
$zipFile->addFromString($entryName, $contents, \PhpZip\Constants\ZipCompressionMethod::DEFLATED); // Deflate сжатие
$zipFile->addFromString($entryName, $contents, \PhpZip\Constants\ZipCompressionMethod::BZIP2); // BZIP2 сжатие
```
<a name="Documentation-ZipFile-addFromStream"></a> **ZipFile::addFromStream** - добавляет в ZIP-архив запись из потока.
```php
// $stream = fopen(..., 'rb');

$zipFile->addFromStream($stream, $entryName);

// можно указать метод сжатия
$zipFile->addFromStream($stream, $entryName, \PhpZip\Constants\ZipCompressionMethod::STORED); // Без сжатия
$zipFile->addFromStream($stream, $entryName, \PhpZip\Constants\ZipCompressionMethod::DEFLATED); // Deflate сжатие
$zipFile->addFromStream($stream, $entryName, \PhpZip\Constants\ZipCompressionMethod::BZIP2); // BZIP2 сжатие
```
<a name="Documentation-ZipFile-addEmptyDir"></a> **ZipFile::addEmptyDir** - добавляет в ZIP-архив новую (пустую) директорию.
```php
// $path = "path/to/";

$zipFile->addEmptyDir($path);
// или
$zipFile[$path] = null;
```
<a name="Documentation-ZipFile-addAll"></a> **ZipFile::addAll** - добавляет все записи из массива.
```php
$entries = [
    'file.txt' => 'file contents', // запись из строки данных
    'empty dir/' => null, // пустой каталог
    'path/to/file.jpg' => fopen('..../filename', 'r'), // запись из потока
    'path/to/file.dat' => new \SplFileInfo('..../filename'), // запись из файла
];

$zipFile->addAll($entries);
```
<a name="Documentation-ZipFile-addDir"></a> **ZipFile::addDir** - добавляет файлы из директории по указанному пути без вложенных директорий.
```php
$zipFile->addDir($dirName);

// можно указать путь в архиве в который необходимо поместить записи
$localPath = "to/path/";
$zipFile->addDir($dirName, $localPath);

// можно указать метод сжатия
$zipFile->addDir($dirName, $localPath, \PhpZip\Constants\ZipCompressionMethod::STORED); // Без сжатия
$zipFile->addDir($dirName, $localPath, \PhpZip\Constants\ZipCompressionMethod::DEFLATED); // Deflate сжатие
$zipFile->addDir($dirName, $localPath, \PhpZip\Constants\ZipCompressionMethod::BZIP2); // BZIP2 сжатие
```
<a name="Documentation-ZipFile-addDirRecursive"></a> **ZipFile::addDirRecursive** - добавляет файлы из директории по указанному пути c вложенными директориями.
```php
$zipFile->addDirRecursive($dirName);

// можно указать путь в архиве в который необходимо поместить записи
$localPath = "to/path/";
$zipFile->addDirRecursive($dirName, $localPath);

// можно указать метод сжатия
$zipFile->addDirRecursive($dirName, $localPath, \PhpZip\Constants\ZipCompressionMethod::STORED); // Без сжатия
$zipFile->addDirRecursive($dirName, $localPath, \PhpZip\Constants\ZipCompressionMethod::DEFLATED); // Deflate сжатие
$zipFile->addDirRecursive($dirName, $localPath, \PhpZip\Constants\ZipCompressionMethod::BZIP2); // BZIP2 сжатие
```
<a name="Documentation-ZipFile-addFilesFromIterator"></a> **ZipFile::addFilesFromIterator** - добавляет файлы из итератора директорий.
```php
// $directoryIterator = new \DirectoryIterator($dir); // без вложенных директорий
// $directoryIterator = new \RecursiveDirectoryIterator($dir); // с вложенными директориями

$zipFile->addFilesFromIterator($directoryIterator);

// можно указать путь в архиве в который необходимо поместить записи
$localPath = "to/path/";
$zipFile->addFilesFromIterator($directoryIterator, $localPath);
// или
$zipFile[$localPath] = $directoryIterator;

// можно указать метод сжатия
$zipFile->addFilesFromIterator($directoryIterator, $localPath, \PhpZip\Constants\ZipCompressionMethod::STORED); // Без сжатия
$zipFile->addFilesFromIterator($directoryIterator, $localPath, \PhpZip\Constants\ZipCompressionMethod::DEFLATED); // Deflate сжатие
$zipFile->addFilesFromIterator($directoryIterator, $localPath, \PhpZip\Constants\ZipCompressionMethod::BZIP2); // BZIP2 сжатие
```
Пример добавления файлов из директории в архив с игнорированием некоторых файлов при помощи итератора директорий.
```php
$ignoreFiles = [
    "file_ignore.txt", 
    "dir_ignore/sub dir ignore/"
];

// $directoryIterator = new \DirectoryIterator($dir); // без вложенных директорий
// $directoryIterator = new \RecursiveDirectoryIterator($dir); // с вложенными директориями
 
// используйте \PhpZip\Util\Iterator\IgnoreFilesFilterIterator для не рекурсивного поиска
$ignoreIterator = new \PhpZip\Util\Iterator\IgnoreFilesRecursiveFilterIterator(
    $directoryIterator, 
    $ignoreFiles
);

$zipFile->addFilesFromIterator($ignoreIterator);
```
<a name="Documentation-ZipFile-addFilesFromGlob"></a> **ZipFile::addFilesFromGlob** - добавляет файлы из директории в соответствии с [glob шаблоном](https://en.wikipedia.org/wiki/Glob_(programming)) без вложенных директорий.
```php
$globPattern = '**.{jpg,jpeg,png,gif}'; // пример glob шаблона -> добавить все .jpg, .jpeg, .png и .gif файлы

$zipFile->addFilesFromGlob($dir, $globPattern);

// можно указать путь в архиве в который необходимо поместить записи
$localPath = "to/path/";
$zipFile->addFilesFromGlob($dir, $globPattern, $localPath);

// можно указать метод сжатия
$zipFile->addFilesFromGlob($dir, $globPattern, $localPath, \PhpZip\Constants\ZipCompressionMethod::STORED); // Без сжатия
$zipFile->addFilesFromGlob($dir, $globPattern, $localPath, \PhpZip\Constants\ZipCompressionMethod::DEFLATED); // Deflate сжатие
$zipFile->addFilesFromGlob($dir, $globPattern, $localPath, \PhpZip\Constants\ZipCompressionMethod::BZIP2); // BZIP2 сжатие
```
<a name="Documentation-ZipFile-addFilesFromGlobRecursive"></a> **ZipFile::addFilesFromGlobRecursive** - добавляет файлы из директории в соответствии с [glob шаблоном](https://en.wikipedia.org/wiki/Glob_(programming)) c вложенными директориями.
```php
$globPattern = '**.{jpg,jpeg,png,gif}'; // пример glob шаблона -> добавить все .jpg, .jpeg, .png и .gif файлы

$zipFile->addFilesFromGlobRecursive($dir, $globPattern);

// можно указать путь в архиве в который необходимо поместить записи
$localPath = "to/path/";
$zipFile->addFilesFromGlobRecursive($dir, $globPattern, $localPath);

// можно указать метод сжатия
$zipFile->addFilesFromGlobRecursive($dir, $globPattern, $localPath, \PhpZip\Constants\ZipCompressionMethod::STORED); // Без сжатия
$zipFile->addFilesFromGlobRecursive($dir, $globPattern, $localPath, \PhpZip\Constants\ZipCompressionMethod::DEFLATED); // Deflate сжатие
$zipFile->addFilesFromGlobRecursive($dir, $globPattern, $localPath, \PhpZip\Constants\ZipCompressionMethod::BZIP2); // BZIP2 сжатие
```
<a name="Documentation-ZipFile-addFilesFromRegex"></a> **ZipFile::addFilesFromRegex** - добавляет файлы из директории в соответствии с [регулярным выражением](https://en.wikipedia.org/wiki/Regular_expression) без вложенных директорий.
```php
$regexPattern = '/\.(jpe?g|png|gif)$/si'; // пример регулярного выражения -> добавить все .jpg, .jpeg, .png и .gif файлы

$zipFile->addFilesFromRegex($dir, $regexPattern);

// можно указать путь в архиве в который необходимо поместить записи
$localPath = "to/path/";
$zipFile->addFilesFromRegex($dir, $regexPattern, $localPath);

// можно указать метод сжатия
$zipFile->addFilesFromRegex($dir, $regexPattern, $localPath, \PhpZip\Constants\ZipCompressionMethod::STORED); // Без сжатия
$zipFile->addFilesFromRegex($dir, $regexPattern, $localPath, \PhpZip\Constants\ZipCompressionMethod::DEFLATED); // Deflate сжатие
$zipFile->addFilesFromRegex($dir, $regexPattern, $localPath, \PhpZip\Constants\ZipCompressionMethod::BZIP2); // BZIP2 сжатие
```
<a name="Documentation-ZipFile-addFilesFromRegexRecursive"></a> **ZipFile::addFilesFromRegexRecursive** - добавляет файлы из директории в соответствии с [регулярным выражением](https://en.wikipedia.org/wiki/Regular_expression) с вложенными директориями.
```php
$regexPattern = '/\.(jpe?g|png|gif)$/si'; // пример регулярного выражения -> добавить все .jpg, .jpeg, .png и .gif файлы

$zipFile->addFilesFromRegexRecursive($dir, $regexPattern);

// можно указать путь в архиве в который необходимо поместить записи
$localPath = "to/path/";
$zipFile->addFilesFromRegexRecursive($dir, $regexPattern, $localPath);

// можно указать метод сжатия
$zipFile->addFilesFromRegexRecursive($dir, $regexPattern, $localPath, \PhpZip\Constants\ZipCompressionMethod::STORED); // Без сжатия
$zipFile->addFilesFromRegexRecursive($dir, $regexPattern, $localPath, \PhpZip\Constants\ZipCompressionMethod::DEFLATED); // Deflate сжатие
$zipFile->addFilesFromRegexRecursive($dir, $regexPattern, $localPath, \PhpZip\Constants\ZipCompressionMethod::BZIP2); // BZIP2 сжатие
```
#### <a name="Documentation-Remove-Zip-Entries"></a> Удаление записей из архива
<a name="Documentation-ZipFile-deleteFromName"></a> **ZipFile::deleteFromName** - удаляет запись по имени.
```php
$zipFile->deleteFromName($entryName);
```
<a name="Documentation-ZipFile-deleteFromGlob"></a> **ZipFile::deleteFromGlob** - удаляет записи в соответствии с [glob шаблоном](https://en.wikipedia.org/wiki/Glob_(programming)).
```php
$globPattern = '**.{jpg,jpeg,png,gif}'; // пример glob шаблона -> удалить все .jpg, .jpeg, .png и .gif файлы

$zipFile->deleteFromGlob($globPattern);
```
<a name="Documentation-ZipFile-deleteFromRegex"></a> **ZipFile::deleteFromRegex** - удаляет записи в соответствии с [регулярным выражением](https://en.wikipedia.org/wiki/Regular_expression).
```php
$regexPattern = '/\.(jpe?g|png|gif)$/si'; // пример регулярному выражения -> удалить все .jpg, .jpeg, .png и .gif файлы

$zipFile->deleteFromRegex($regexPattern);
```
<a name="Documentation-ZipFile-deleteAll"></a> **ZipFile::deleteAll** - удаляет все записи в ZIP-архиве.
```php
$zipFile->deleteAll();
```
#### <a name="Documentation-Entries"></a> Работа с записями и с архивом
<a name="Documentation-ZipFile-rename"></a> **ZipFile::rename** - переименовывает запись по имени.
```php
$zipFile->rename($oldName, $newName);
```
<a name="Documentation-ZipFile-setCompressionLevel"></a> **ZipFile::setCompressionLevel** - устанавливает уровень сжатия для всех файлов, находящихся в архиве.

> _Обратите внимание, что действие данного метода не распространяется на записи, добавленные после выполнения этого метода._

По умолчанию используется уровень сжатия 5 (`\PhpZip\Constants\ZipCompressionLevel::NORMAL`) или уровень сжатия, определённый в архиве для Deflate сжатия.

Поддерживаются диапазон значений от 1 (`\PhpZip\Constants\ZipCompressionLevel::SUPER_FAST`) до 9 (`\PhpZip\Constants\ZipCompressionLevel::MAXIMUM`). Чем выше число, тем лучше и дольше сжатие.
```php
$zipFile->setCompressionLevel(\PhpZip\Constants\ZipCompressionLevel::MAXIMUM);
```
<a name="Documentation-ZipFile-setCompressionLevelEntry"></a> **ZipFile::setCompressionLevelEntry** - устанавливает уровень сжатия для определённой записи в архиве.

Поддерживаются диапазон значений от 1 (`\PhpZip\Constants\ZipCompressionLevel::SUPER_FAST`) до 9 (`\PhpZip\Constants\ZipCompressionLevel::MAXIMUM`). Чем выше число, тем лучше и дольше сжатие.
```php
$zipFile->setCompressionLevelEntry($entryName, \PhpZip\Constants\ZipCompressionLevel::MAXIMUM);
```
<a name="Documentation-ZipFile-setCompressionMethodEntry"></a> **ZipFile::setCompressionMethodEntry** - устанавливает метод сжатия для определённой записи в архиве.

Доступны следующие методы сжатия:
- `\PhpZip\Constants\ZipCompressionMethod::STORED` - без сжатия
- `\PhpZip\Constants\ZipCompressionMethod::DEFLATED` - Deflate сжатие
- `\PhpZip\Constants\ZipCompressionMethod::BZIP2` - Bzip2 сжатие при наличии расширения `ext-bz2`
```php
$zipFile->setCompressionMethodEntry($entryName, \PhpZip\Constants\ZipCompressionMethod::DEFLATED);
```
<a name="Documentation-ZipFile-setArchiveComment"></a> **ZipFile::setArchiveComment** - устанавливает комментарий к ZIP-архиву.
```php
$zipFile->setArchiveComment($commentArchive);
```
<a name="Documentation-ZipFile-setEntryComment"></a> **ZipFile::setEntryComment** - устанавливает комментарий к записи, используя её имя.
```php
$zipFile->setEntryComment($entryName, $comment);
```
<a name="Documentation-ZipFile-matcher"></a> **ZipFile::matcher** - выборка записей в архиве для проведения операций над выбранными записями.
```php
$matcher = $zipFile->matcher();
```
Выбор файлов из архива по одному:
```php
$matcher
    ->add('entry name')
    ->add('another entry');
```
Выбор нескольких файлов в архиве:
```php
$matcher->add([
    'entry name',
    'another entry name',
    'path/'
]);
```
Выбор файлов по регулярному выражению:
```php
$matcher->match('~\.jpe?g$~i');
```
Выбор всех файлов в архиве:
```php
$matcher->all();
```
count() - получает количество выбранных записей:
```php
$count = count($matcher);
// или
$count = $matcher->count();
```
getMatches() - получает список выбранных записей:
```php
$entries = $matcher->getMatches();
// пример содержимого: ['entry name', 'another entry name'];
```
invoke() - выполняет пользовательскую функцию над выбранными записями:
```php
// пример
$matcher->invoke(function($entryName) use($zipFile) {
    $newName = preg_replace('~\.(jpe?g)$~i', '.no_optimize.$1', $entryName);
    $zipFile->rename($entryName, $newName);
});
```
Функции для работы над выбранными записями:
```php
$matcher->delete(); // удалет выбранные записи из ZIP-архива
$matcher->setPassword($password); // устанавливает новый пароль на выбранные записи
$matcher->setPassword($password, $encryptionMethod); // устанавливает новый пароль и метод шифрования на выбранные записи
$matcher->setEncryptionMethod($encryptionMethod); // устанавливает метод шифрования на выбранные записи
$matcher->disableEncryption(); // отключает шифрование для выбранных записей
```
#### <a name="Documentation-Password"></a> Работа с паролями

Реализована поддержка методов шифрования:
- `\PhpZip\Constants\ZipEncryptionMethod::PKWARE` - Traditional PKWARE encryption
- `\PhpZip\Constants\ZipEncryptionMethod::WINZIP_AES_256` - WinZip AES encryption 256 bit (рекомендуемое)
- `\PhpZip\Constants\ZipEncryptionMethod::WINZIP_AES_192` - WinZip AES encryption 192 bit
- `\PhpZip\Constants\ZipEncryptionMethod::WINZIP_AES_128` - WinZip AES encryption 128 bit

<a name="Documentation-ZipFile-setReadPassword"></a> **ZipFile::setReadPassword** - устанавливает пароль на чтение открытого запароленного архива для всех зашифрованных записей.

> _Установка пароля не является обязательной для добавления новых записей или удаления существующих, но если вы захотите извлечь контент или изменить метод/уровень сжатия, метод шифрования или изменить пароль, то в этом случае пароль необходимо указать._
```php
$zipFile->setReadPassword($password);
```
<a name="Documentation-ZipFile-setReadPasswordEntry"></a> **ZipFile::setReadPasswordEntry** - устанавливает пароль на чтение конкретной зашифрованной записи открытого запароленного архива.
```php
$zipFile->setReadPasswordEntry($entryName, $password);
```
<a name="Documentation-ZipFile-setPassword"></a> **ZipFile::setPassword** - устанавливает новый пароль для всех файлов, находящихся в архиве.

> _Обратите внимание, что действие данного метода не распространяется на записи, добавленные после выполнения этого метода._
```php
$zipFile->setPassword($password);
```
Можно установить метод шифрования:
```php
$encryptionMethod = \PhpZip\Constants\ZipEncryptionMethod::WINZIP_AES_256;
$zipFile->setPassword($password, $encryptionMethod);
```
<a name="Documentation-ZipFile-setPasswordEntry"></a> **ZipFile::setPasswordEntry** - устанавливает новый пароль для конкретного файла.
```php
$zipFile->setPasswordEntry($entryName, $password);
```
Можно установить метод шифрования:
```php
$encryptionMethod = \PhpZip\Constants\ZipEncryptionMethod::WINZIP_AES_256;
$zipFile->setPasswordEntry($entryName, $password, $encryptionMethod);
```
<a name="Documentation-ZipFile-disableEncryption"></a> **ZipFile::disableEncryption** - отключает шифрования всех записей, находящихся в архиве.

> _Обратите внимание, что действие данного метода не распространяется на записи, добавленные после выполнения этого метода._
```php
$zipFile->disableEncryption();
```
<a name="Documentation-ZipFile-disableEncryptionEntry"></a> **ZipFile::disableEncryptionEntry** - отключает шифрование записи по её имени.
```php
$zipFile->disableEncryptionEntry($entryName);
```
#### <a name="Documentation-ZipAlign-Usage"></a> zipalign
<a name="Documentation-ZipFile-setZipAlign"></a> **ZipFile::setZipAlign** - устанавливает выравнивание архива для оптимизации APK файлов (Android packages).

Метод добавляет паддинги незашифрованным и не сжатым записям, для оптимизации расхода памяти в системе Android. Рекомендуется использовать для `APK` файлов. Файл может незначительно увеличиться.

Этот метод является альтернативой вызова команды `zipalign -f -v 4 filename.zip`.

Подробнее можно ознакомиться по [ссылке](https://developer.android.com/studio/command-line/zipalign.html).
```php
// вызовите до сохранения или вывода архива
$zipFile->setZipAlign(4);
```
#### <a name="Documentation-Unchanged"></a> Отмена изменений
<a name="Documentation-ZipFile-unchangeAll"></a> **ZipFile::unchangeAll** - отменяет все изменения, сделанные в архиве.
```php
$zipFile->unchangeAll();
```
<a name="Documentation-ZipFile-unchangeArchiveComment"></a> **ZipFile::unchangeArchiveComment** - отменяет изменения в комментарии к архиву.
```php
$zipFile->unchangeArchiveComment();
```
<a name="Documentation-ZipFile-unchangeEntry"></a> **ZipFile::unchangeEntry** - отменяет изменения для конкретной записи архива.
```php
$zipFile->unchangeEntry($entryName);
```
#### <a name="Documentation-Save-Or-Output-Entries"></a> Сохранение файла или вывод в браузер
<a name="Documentation-ZipFile-saveAsFile"></a> **ZipFile::saveAsFile** - сохраняет архив в файл.
```php
$zipFile->saveAsFile($filename);
```
<a name="Documentation-ZipFile-saveAsStream"></a> **ZipFile::saveAsStream** - записывает архив в поток.
```php
// $fp = fopen($filename, 'w+b');

$zipFile->saveAsStream($fp);
```
<a name="Documentation-ZipFile-outputAsString"></a> **ZipFile::outputAsString** - выводит ZIP-архив в виде строки.
```php
$rawZipArchiveBytes = $zipFile->outputAsString();
```
<a name="Documentation-ZipFile-outputAsAttachment"></a> **ZipFile::outputAsAttachment** - выводит ZIP-архив в браузер.

При выводе устанавливаются необходимые заголовки, а после вывода завершается работа скрипта.
```php
$zipFile->outputAsAttachment($outputFilename);
```
Можно установить MIME-тип:
```php
$mimeType = 'application/zip'
$zipFile->outputAsAttachment($outputFilename, $mimeType);
```
<a name="Documentation-ZipFile-outputAsResponse"></a> **ZipFile::outputAsResponse** - выводит ZIP-архив, как Response [PSR-7](http://www.php-fig.org/psr/psr-7/).

Метод вывода может использоваться в любом PSR-7 совместимом фреймворке. 
```php
// $response = ....; // instance Psr\Http\Message\ResponseInterface
$zipFile->outputAsResponse($response, $outputFilename);
```
Можно установить MIME-тип:
```php
$mimeType = 'application/zip'
$zipFile->outputAsResponse($response, $outputFilename, $mimeType);
```
Пример для Slim Framework:
```php
$app = new \Slim\App;
$app->get('/download', function ($req, $res, $args) {
    $zipFile = new \PhpZip\ZipFile();
    $zipFile['file.txt'] = 'content';
    return $zipFile->outputAsResponse($res, 'file.zip');
});
$app->run();
```
<a name="Documentation-ZipFile-rewrite"></a> **ZipFile::rewrite** - сохраняет изменения и заново открывает изменившийся архив.
```php
$zipFile->rewrite();
```
#### <a name="Documentation-Close-Zip-Archive"></a> Закрытие архива
<a name="Documentation-ZipFile-close"></a> **ZipFile::close** - закрывает ZIP-архив.
```php
$zipFile->close();
```
### <a name="Running-Tests"></a> Запуск тестов
Установите зависимости для разработки.
```bash
composer install --dev
```
Запустите тесты:
```bash
vendor/bin/phpunit -v -c phpunit.xml
```
### <a name="Changelog"></a> История изменений
История изменений на [странице релизов](https://github.com/Ne-Lexa/php-zip/releases).

### <a name="Upgrade"></a> Обновление версий
#### <a name="Upgrade-v2-to-v3"></a> Обновление с версии 2 до версии 3.0
Обновите мажорную версию в файле `composer.json` до `^3.0`.
```json
{
    "require": {
        "nelexa/zip": "^3.0"
    }
}
```
Затем установите обновления с помощью `Composer`:
```bash
composer update nelexa/zip
```
Обновите ваш код для работы с новой версией:
- Класс `ZipOutputFile` объединён с `ZipFile` и удалён.
  + Замените `new \PhpZip\ZipOutputFile()` на `new \PhpZip\ZipFile()`
- Статичиская инициализация методов стала не статической.
  + Замените `\PhpZip\ZipFile::openFromFile($filename);` на `(new \PhpZip\ZipFile())->openFile($filename);`
  + Замените `\PhpZip\ZipOutputFile::openFromFile($filename);` на `(new \PhpZip\ZipFile())->openFile($filename);`
  + Замените `\PhpZip\ZipFile::openFromString($contents);` на `(new \PhpZip\ZipFile())->openFromString($contents);`
  + Замените `\PhpZip\ZipFile::openFromStream($stream);` на `(new \PhpZip\ZipFile())->openFromStream($stream);`
  + Замените `\PhpZip\ZipOutputFile::create()` на `new \PhpZip\ZipFile()`
  + Замените `\PhpZip\ZipOutputFile::openFromZipFile($zipFile)` на `(new \PhpZip\ZipFile())->openFile($filename);`
- Переименуйте методы:
  + `addFromFile` в `addFile`
  + `setLevel` в `setCompressionLevel`
  + `ZipFile::setPassword` в `ZipFile::withReadPassword`
  + `ZipOutputFile::setPassword` в `ZipFile::withNewPassword`
  + `ZipOutputFile::disableEncryptionAllEntries` в `ZipFile::withoutPassword`
  + `ZipOutputFile::setComment` в `ZipFile::setArchiveComment`
  + `ZipFile::getComment` в `ZipFile::getArchiveComment`
- Изменились сигнатуры для методов `addDir`, `addFilesFromGlob`, `addFilesFromRegex`.
- Удалены методы:
  + `getLevel`
  + `setCompressionMethod`
  + `setEntryPassword`
