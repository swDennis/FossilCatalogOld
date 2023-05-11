<?php

namespace App\Services\Image\ThumbnailGenerator;

use App\Exceptions\ThumbnailGeneratorHandlerNotFoundException;
use App\Services\Image\ThumbnailGenerator\Handler\JpegHandler;
use App\Services\Image\ThumbnailGenerator\Handler\PngHandler;
use App\Services\Image\ThumbnailGenerator\Handler\ThumbnailCreationHandlerInterface;

class ThumbnailGenerator implements ThumbnailGeneratorInterface
{
    /**
     * @var array<int, ThumbnailCreationHandlerInterface>
     */
    private array $handler = [];

    public function __construct()
    {
        $this->addHandler(new JpegHandler());
        $this->addHandler(new PngHandler());
    }

    public function addHandler(ThumbnailCreationHandlerInterface $handler): void
    {
        $this->handler[] = $handler;
    }

    public function generate(string $imagePath, string $thumbnailTargetPath, string $mimeType): void
    {
        $handler = $this->getHandler($mimeType);

        $handler->create($imagePath, $thumbnailTargetPath);
    }

    private function getHandler(string $mimeType): ThumbnailCreationHandlerInterface
    {
        foreach ($this->handler as $thumbnailGenerator) {
            if ($thumbnailGenerator->supports($mimeType)) {
                return $thumbnailGenerator;
            }
        }

        throw new ThumbnailGeneratorHandlerNotFoundException($mimeType);
    }
}