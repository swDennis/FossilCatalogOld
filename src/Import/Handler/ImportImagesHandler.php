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
        if (!$file) {
            throw new \UnexpectedValueException('Expects file to import images');
        }

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

            $array = json_decode($line, true);
            if (!is_array($array)) {
                throw new \UnexpectedValueException('Expect array got ' . gettype($array));
            }

            $image = new Image();
            $image->fromArray($array);

            $this->copyImages($image);
            /** @phpstan-ignore-next-line */
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
        $imageTargetBasePath = $this->appKernel->getProjectDir() . '/public';
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