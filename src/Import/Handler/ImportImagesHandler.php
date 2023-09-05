<?php

namespace App\Import\Handler;

use App\Entity\Image;
use App\Import\ImportStatus;
use App\Repository\ImageRepositoryInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpKernel\KernelInterface;

class ImportImagesHandler extends AbstractImportHandler
{
    public function __construct(
        private readonly RequestStack $requestStack,
        private readonly ImageRepositoryInterface $imageRepository,
        private readonly KernelInterface $appKernel
    ) {
        parent::__construct($this->requestStack);
    }

    public function getKey(): string
    {
        return 'imageStatus';
    }

    public function getFileName(): string
    {
        return 'Image.csv';
    }

    public function import(): ImportStatus
    {
        $this->status = $this->getStatusFromSession();

        $file = fopen($this->status->getFile(), 'rb');

        $offset = $this->status->getImported();
        for ($i = 0; $i < $offset; $i++) {
            fgets($file);
        }

        $lineCounter = 0;
        for ($i = 0; $i < self::IMPORT_LIMIT; $i++) {
            $line = fgets($file);
            if (empty($line)) {
                break;
            }

            $array = explode(',', $line);

            $image = new Image();
            $image->fromArray([
                'id' => $array[0],
                'fossilId' => $array[1],
                'mimeType' => $array[2],
                'imageName' => $array[3],
                'thumbnailName' => $array[4],
                'relativePath' => $array[5],
                'relativeImagePath' => $array[6],
                'relativeThumbnailPath' => $array[7],
                'absolutePath' => $array[8],
                'absoluteImagePath' => $array[9],
                'absoluteThumbnailPath' => $array[10],
                'showInGallery' => (bool) $array[11],
                'isMainImage' => (bool) preg_replace('/[\x00-\x1F\x7F]/u', '', $array[12]),
            ]);

            $this->copyImages($image);
            $this->imageRepository->saveImage($image, true);

            $lineCounter++;
        }

        fclose($file);

        $this->status->add($lineCounter);

        if ($this->status->getImported() >= $this->status->getToImport()) {
            $this->status->finish();
        }

        $this->saveSession();

        return $this->status;
    }

    private function copyImages(Image $image): void
    {
        $imageBasePath = str_replace('/' . $this->getFileName(), '', $this->status->getFile());
        $imageTargetBasePath = $this->appKernel->getProjectDir();
        $imageRelativePath = str_replace('images/', '', $image->getRelativeImagePath());
        $thumbnailRelativePath = str_replace('images/', '', $image->getRelativeThumbnailPath());

        $filesystem = new Filesystem();
        if ($filesystem->exists($imageBasePath . '/' . $imageRelativePath)) {
            $filesystem->copy($imageBasePath . '/' . $imageRelativePath, $imageTargetBasePath . '/' . $image->getRelativeImagePath());
        }

        if ($filesystem->exists($imageBasePath . '/' . $thumbnailRelativePath)) {
            $filesystem->copy($imageBasePath . '/' . $thumbnailRelativePath, $imageTargetBasePath . '/' . $image->getRelativeThumbnailPath());
        }
    }
}