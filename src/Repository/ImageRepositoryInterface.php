<?php

namespace App\Repository;

use App\Entity\Image;

interface ImageRepositoryInterface
{
    public const IMAGE_TABLE_NAME = 'image';

    public function saveImage(Image $image): Image;

    public function getImageById(int $id): ?Image;

    /**
     * @return array<Image>
     */
    public function getImagesByFossilId(int $fossilId): array;

    public function getMainImageByFossilId(int $fossilId): ?Image;

    /**
     * @param array<int> $ids
     * @return array<Image>
     */
    public function getImagesForFossils(array $ids): array;

    public function getRandomTitleImage(): ?Image;

    public function deleteImage(int $imageId): void;

    /**
     * @return array<int, array<string, mixed>>
     */
    public function getExportList(int $limit, int $offset): array;

    public function getColumnCount(): int;


}