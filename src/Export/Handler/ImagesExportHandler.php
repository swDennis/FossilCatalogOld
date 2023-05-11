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
        private readonly RequestStack $requestStack
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
            \file_put_contents($this->targetFile, implode(',', $line) . PHP_EOL, FILE_APPEND);

            if (is_file($line['absoluteImagePath'])) {
                $zip->addFile($line['absoluteImagePath'], str_replace('images/', '', $line['relativeImagePath']));

            }
            if (is_file($line['absoluteThumbnailPath'])) {
                $zip->addFile($line['absoluteThumbnailPath'], str_replace('images/', '', $line['relativeThumbnailPath']));
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

    private function connectPathAndFileName(string $path, string $fileName): string
    {
        return sprintf('%s/%s', $path, $fileName);
    }

    public function getKey(): string
    {
        return 'imageStatus';
    }

    public function getFileName(): string
    {
        return 'Image.csv';
    }
}