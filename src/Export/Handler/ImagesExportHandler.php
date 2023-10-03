<?php

namespace App\Export\Handler;

use App\Export\Exceptions\CreateZipException;
use App\Export\ExportServiceInterface;
use App\Export\ExportStatus;
use App\Repository\ImageRepositoryInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use ZipArchive;

class ImagesExportHandler extends AbstractHandler
{
    private const EXPORT_LIMIT = 20;

    public function __construct(
        private readonly ImageRepositoryInterface $imageRepository,
        private readonly RequestStack             $requestStack
    ) {
        parent::__construct($this->requestStack);
    }

    public function analyzeData(): ExportStatus
    {
        $toExport = $this->imageRepository->getColumnCount();

        $tagStatus = new ExportStatus($this->getKey(), false, $toExport);

        $this->saveSession($tagStatus);

        return $tagStatus;
    }

    public function export(): ExportStatus
    {
        $status = $this->getStatusFromSession();

        if ($status->getIsFinished()) {
            return $status;
        }

        $data = $this->imageRepository->getExportList(self::EXPORT_LIMIT, $status->getExported());

        $zipTargetDirectory = dirname($this->targetFile);
        $zipName = basename($zipTargetDirectory) . ExportServiceInterface::ZIP_FILE_EXTENSION;
        $zipFile = $this->connectPathAndFileName($zipTargetDirectory, $zipName);

        $zip = new ZipArchive();
        if ($zip->open($zipFile, ZipArchive::CREATE) !== true) {
            throw new CreateZipException('Cannot create zip file for backup');
        }

        foreach ($data as $line) {
            \file_put_contents($this->targetFile, json_encode($line) . PHP_EOL, FILE_APPEND);

            $absoluteImagePath = $this->getPath('absoluteImagePath', $line);
            $relativeImagePath = $this->getPath('relativeImagePath', $line);
            if (is_file($absoluteImagePath)) {
                $zip->addFile($absoluteImagePath, str_replace('images/', '', $relativeImagePath));

            }

            $absoluteThumbnailPath = $this->getPath('absoluteThumbnailPath', $line);
            $relativeThumbnailPath = $this->getPath('relativeThumbnailPath', $line);

            if (is_file($absoluteThumbnailPath)) {
                $zip->addFile($absoluteThumbnailPath, str_replace('images/', '', $relativeThumbnailPath));
            }
        }

        $zip->close();

        $status->add(count($data));

        if ($status->getExported() >= $status->getToExport()) {
            $status->finish();
        }

        $this->saveSession($status);

        return $status;
    }

    public function getKey(): string
    {
        return 'imageStatus';
    }

    public function getFileName(): string
    {
        return 'Image.csv';
    }

    private function connectPathAndFileName(string $path, string $fileName): string
    {
        return sprintf('%s/%s', $path, $fileName);
    }

    /**
     * @param array<string, mixed> $array
     */
    private function getPath(string $arrayKey, array $array): string
    {
        $value = $array[$arrayKey];

        if (!is_string($value)) {
            throw new \UnexpectedValueException(sprintf('Expect string for %s got %s', $arrayKey, gettype($value)));
        }

        return $value;
    }
}