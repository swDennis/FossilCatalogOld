<?php

namespace App\Services\Image\ThumbnailGenerator\Handler;

class JpegHandler implements ThumbnailCreationHandlerInterface
{
    public function supports(string $mimeType): bool
    {
        return $mimeType === self::MIMETYPE_JPG;
    }

    public function create(string $imageSourcePath, string $thumbnailTargetPath): void
    {
        $sourceImage = imagecreatefromjpeg($imageSourcePath);
        $orgWidth = imagesx($sourceImage);
        $orgHeight = imagesy($sourceImage);

        $thumbHeight = floor($orgHeight * (self::THUMBNAIL_WIDTH / $orgWidth));
        $destImage = imagecreatetruecolor(self::THUMBNAIL_WIDTH, $thumbHeight);

        imagecopyresampled(
            $destImage,
            $sourceImage,
            0,
            0,
            0,
            0,
            self::THUMBNAIL_WIDTH,
            $thumbHeight,
            $orgWidth,
            $orgHeight
        );

        imagejpeg($destImage, $thumbnailTargetPath);
        imagedestroy($sourceImage);
        imagedestroy($destImage);
    }
}