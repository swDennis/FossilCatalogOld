<?php

namespace App\Repository;

use App\Entity\Image;

interface ImageRepositoryInterface
{
    public const IMAGE_TABLE_NAME = 'image';

    public function saveImage(Image $image): Image;

    public function getImageById(int $id): array;

    public function getImagesByFossilId(int $fossilId): array;

    public function getMainImageByFossilId(int $fossilId): array;

    public function getImagesForFossils(array $ids): array;

    public function deleteImage(int $imageId): void;

    public function getExportList(int $limit, int $offset): array;

    public function getColumnCount(): int;
}