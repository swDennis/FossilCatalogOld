<?php

namespace App\Import;

use App\Export\ExportServiceInterface;
use App\Import\Exception\CannotOpenZipException;
use App\Import\Exception\FileNotFoundException;
use App\Import\Exception\UnexpectedFileNameException;
use App\Import\Exception\UnexpectedMimeTypeException;
use Iterator;
use Symfony\Component\Finder\Finder;
use Symfony\Component\HttpFoundation\File\File;
use ZipArchive;

class ImportFileValidator implements ImportFileValidatorInterface
{
    public function validate(File $file): void
    {
        if ($file->getMimeType() !== self::EXPECTED_MIME_TYPE) {
            $this->deleteFile($file->getPathname());

            throw new UnexpectedMimeTypeException($file->getMimeType());
        }

        $isFileNameMatch = preg_match(self::FILE_REGEX, $file->getFilename(), $match, PREG_OFFSET_CAPTURE);
        if (!$isFileNameMatch) {
            $this->deleteFile($file->getPathname());

            throw new UnexpectedFileNameException();
        }
    }

    public function validateContentStructure(File $file): string
    {
        $directory = $this->extractZipFile($file->getPathname());

        $finder = new Finder();
        $finder->files()->in($directory);

        $files = $finder->getIterator();

        foreach (self::EXPECTED_FILES_IN_IMPORT as $expectedFile) {
            if (!$this->isFileInIterator($expectedFile, $files)) {
                throw new FileNotFoundException($expectedFile);
            }
        }

        return $directory;
    }

    private function extractZipFile(string $filePath): string
    {
        $extractTo = $this->createUnzipDirectory($filePath);

        $zipArchive = new ZipArchive();
        if ($zipArchive->open($filePath) !== true) {
            $this->deleteFile($filePath);

            throw new CannotOpenZipException();
        }

        $zipArchive->extractTo($extractTo);
        $zipArchive->close();

        return $extractTo;
    }

    private function createUnzipDirectory(string $filePath): string
    {
        $directoryName = str_replace(ExportServiceInterface::ZIP_FILE_EXTENSION, '', $filePath);

        if (is_dir($directoryName)) {
            return $directoryName;
        }

        if (!mkdir($directoryName, 0777, true) && !is_dir($directoryName)) {
            $this->deleteFile($filePath);

            throw new \RuntimeException(sprintf('Directory "%s" was not created', $directoryName));
        }

        return $directoryName;
    }

    private function deleteFile(string $filePath): void
    {
        unlink($filePath);
    }

    private function isFileInIterator(string $fileName, Iterator $iterator)
    {
        foreach ($iterator as $file) {
            if ($file->getFilename() === $fileName) {
                return true;
            }
        }

        return false;
    }
}