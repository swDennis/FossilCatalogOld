<?php

namespace App\Services\Image\ThumbnailGenerator;

use App\Services\Image\ThumbnailGenerator\Handler\ThumbnailCreationHandlerInterface;

interface ThumbnailGeneratorInterface
{
    public function addHandler(ThumbnailCreationHandlerInterface $handler): void;

    public function generate(string $imagePath, string $thumbnailTargetPath, string $mimeType): void;
}