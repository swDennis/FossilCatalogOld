<?php

namespace App\Repository;

use App\Entity\Image;
use App\Exceptions\IsNotNumericException;
use Doctrine\DBAL\ArrayParameterType;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Query\QueryBuilder;

class ImageRepository implements ImageRepositoryInterface, RepositoryInterface
{
    public function __construct(public readonly Connection $connection) {}

    public function saveImage(Image $image, ?bool $isNew = null): Image
    {
        if ($isNew === null) {
            $isNew = $image->getId() === null;
        }

        $queryBuilder = $this->createInsertUpdateQueryBuilder($image, $isNew);
        $queryBuilder->executeQuery();

        if (!$isNew) {
            return $image;
        }

        $id = $this->connection->lastInsertId();
        if ($id === false) {
            throw new \RuntimeException('Could not create Image entity');
        }

        $image->setId((int) $id);

        return $image;
    }

    public function getImageById(int $id): ?Image
    {
        $result = $this->connection->createQueryBuilder()
            ->select(['*'])
            ->from(self::IMAGE_TABLE_NAME)
            ->where('id = :id')
            ->setParameter('id', $id)
            ->executeQuery()
            ->fetchAssociative();

        if (!is_array($result)) {
            return null;
        }

        return (new Image())->fromArray($result);
    }

    public function getImagesByFossilId(int $fossilId): array
    {
        $result = $this->connection->createQueryBuilder()
            ->select(['*'])
            ->from(self::IMAGE_TABLE_NAME)
            ->where('fossilId = :fossilId')
            ->setParameter('fossilId', $fossilId)
            ->executeQuery()
            ->fetchAllAssociative();

        if (!is_array($result)) {
            return [];
        }

        return $this->prepareListResult($result);
    }

    public function getMainImageByFossilId(int $fossilId): ?Image
    {
        $result = $this->connection->createQueryBuilder()
            ->select(['*'])
            ->from(self::IMAGE_TABLE_NAME)
            ->where('fossilId = :fossilId')
            ->andWhere('isMainImage = 1')
            ->setParameter('fossilId', $fossilId)
            ->executeQuery()
            ->fetchAssociative();

        if (!is_array($result)) {
            return null;
        }

        return (new Image())->fromArray($result);
    }

    public function getImagesForFossils(array $ids): array
    {
        if (empty($ids)) {
            return [];
        }

        $result = $this->connection->createQueryBuilder()
            ->select(['*'])
            ->from(self::IMAGE_TABLE_NAME)
            ->where('fossilId IN (:fossilId)')
            ->orderBy('isMainImage', 'DESC')
            ->setParameter('fossilId', $ids, ArrayParameterType::INTEGER)
            ->executeQuery()
            ->fetchAllAssociativeIndexed();

        return $this->prepareListResult($result);
    }

    public function getRandomTitleImage(): ?Image
    {
        $ids = $this->connection->createQueryBuilder()
            ->select('id')
            ->from(self::IMAGE_TABLE_NAME)
            ->where('isMainImage = 1')
            ->executeQuery()
            ->fetchFirstColumn();

        if (empty($ids)) {
            return null;
        }

        $randomImageIdArrayKey = array_rand($ids, 1);

        $id = $ids[$randomImageIdArrayKey];
        if (!is_numeric($id)) {
            throw new IsNotNumericException($this);
        }

        return $this->getImageById((int) $id);
    }

    public function deleteImage(int $imageId): void
    {
        $this->connection->createQueryBuilder()
            ->delete(self::IMAGE_TABLE_NAME)
            ->where('id = :id')
            ->setParameter('id', $imageId)
            ->executeQuery();
    }

    public function getExportList(int $limit, int $offset): array
    {
        return $this->connection->createQueryBuilder()
            ->select(['*'])
            ->from(self::IMAGE_TABLE_NAME)
            ->setFirstResult($offset)
            ->setMaxResults($limit)
            ->executeQuery()
            ->fetchAllAssociative();
    }

    public function getColumnCount(): int
    {
        /** @phpstan-ignore-next-line */
        return $this->connection->createQueryBuilder()
            ->select(['COUNT(id)'])
            ->from(self::IMAGE_TABLE_NAME)
            ->executeQuery()
            ->fetchOne();
    }

    /**
     * @param array<array<string,mixed>> $result
     *
     * @return array<Image>
     */
    private function prepareListResult(array $result): array
    {
        $array = [];
        foreach ($result as $fossilFormField) {
            $array[] = (new Image())->fromArray($fossilFormField);
        }

        return $array;
    }

    private function createInsertUpdateQueryBuilder(Image $image, bool $isNewImage): QueryBuilder
    {
        $queryBuilder = $this->connection->createQueryBuilder();
        if ($isNewImage) {
            $function = self::INSERT_FUNCTION;
            $queryBuilder->insert(self::IMAGE_TABLE_NAME);
        } else {
            $function = self::UPDATE_FUNCTION;
            $queryBuilder->update(self::IMAGE_TABLE_NAME);
            $queryBuilder->where('id = :id')
                ->setParameter('id', $image->getId());
        }

        $queryBuilder->$function('fossilId', ':fossilId');
        $queryBuilder->$function('imageName', ':imageName');
        $queryBuilder->$function('thumbnailName', ':thumbnailName');
        $queryBuilder->$function('mimeType', ':mimeType');
        $queryBuilder->$function('relativePath', ':relativePath');
        $queryBuilder->$function('relativeImagePath', ':relativeImagePath');
        $queryBuilder->$function('relativeThumbnailPath', ':relativeThumbnailPath');
        $queryBuilder->$function('absolutePath', ':absolutePath');
        $queryBuilder->$function('absoluteImagePath', ':absoluteImagePath');
        $queryBuilder->$function('absoluteThumbnailPath', ':absoluteThumbnailPath');
        $queryBuilder->$function('showInGallery', ':showInGallery');
        $queryBuilder->$function('isMainImage', ':isMainImage');
        $queryBuilder->setParameter('fossilId', $image->getFossilId())
            ->setParameter('imageName', $image->getImageName())
            ->setParameter('thumbnailName', $image->getThumbnailName())
            ->setParameter('mimeType', $image->getMimeType())
            ->setParameter('relativePath', $image->getRelativePath())
            ->setParameter('relativeImagePath', $image->getRelativeImagePath())
            ->setParameter('relativeThumbnailPath', $image->getRelativeThumbnailPath())
            ->setParameter('absolutePath', $image->getAbsolutePath())
            ->setParameter('absoluteImagePath', $image->getAbsoluteImagePath())
            ->setParameter('absoluteThumbnailPath', $image->getAbsoluteThumbnailPath())
            ->setParameter('showInGallery', (int) $image->getShowInGallery())
            ->setParameter('isMainImage', (int) $image->getIsMainImage());

        return $queryBuilder;
    }
}