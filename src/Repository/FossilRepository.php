<?php

namespace App\Repository;

use App\Entity\Fossil;
use App\Entity\FossilEntity;
use App\Exceptions\IsNotNumericException;
use App\Services\Filter\FilterQueryQueryFactory;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Query\QueryBuilder;

class FossilRepository implements FossilRepositoryInterface, RepositoryInterface
{
    public function __construct(
        private readonly Connection                         $connection,
        private readonly FilterQueryQueryFactory            $filterFactory,
        private readonly FossilFormFieldRepositoryInterface $fossilFormFieldRepository,
        private readonly ImageRepositoryInterface           $imageRepository,
        private readonly TagRepositoryInterface             $tagRepository
    ) {}

    public function saveFossil(FossilEntity $fossil, ?bool $isNew = null): FossilEntity
    {
        if ($isNew === null) {
            $isNew = $fossil->getId() === null;
        }

        $queryBuilder = $this->createInsertUpdateQueryBuilder($fossil, $isNew);

        $queryBuilder->executeQuery();

        if (!$isNew) {
            $this->addCategoriesAndTags($fossil);

            return $fossil;
        }

        $id = $this->connection->lastInsertId();
        if ($id === false) {
            throw new \RuntimeException('Could not create Fossil entity');
        }

        $fossil->setId((int) $id);

        $this->addCategoriesAndTags($fossil);

        return $fossil;
    }

    public function getFossilById(int $id): ?FossilEntity
    {
        $fossil = $this->connection->createQueryBuilder()
            ->select(['*'])
            ->from(FossilRepositoryInterface::FOSSIL_TABLE_NAME)
            ->where('id = :id')
            ->setParameter('id', $id)
            ->executeQuery()
            ->fetchAssociative();

        if (empty($fossil)) {
            return null;
        }

        $fossilEntity = (new FossilEntity())->fromArray($fossil);
        $fossilId = $fossilEntity->getId();
        if ($fossilId === null) {
            return $fossilEntity;
        }

        $fossilEntity->setImages($this->imageRepository->getImagesByFossilId($fossilId));
        $fossilEntity->setCategories($this->tagRepository->getByFossilId($fossilId, TagRepositoryInterface::GET_ONLY_CATEGORIES));
        $fossilEntity->setTags($this->tagRepository->getByFossilId($fossilId, TagRepositoryInterface::GET_ONLY_TAGS));

        return $fossilEntity;
    }

    public function getFossilList(array $filter): array
    {
        $page = $filter['page'];

        $firstResult = 0;
        if ($page > 1) {
            $firstResult = ($page - 1) * self::FOSSILS_PER_PAGE;
        }

        $queryBuilder = $this->connection->createQueryBuilder()
            ->select(['*'])
            ->from(FossilRepositoryInterface::FOSSIL_TABLE_NAME)
            ->setFirstResult($firstResult)
            ->setMaxResults(self::FOSSILS_PER_PAGE)
            ->orderBy('id');


        $this->filterFactory->addFilter($filter, $queryBuilder);

        $fossils = $queryBuilder->executeQuery()->fetchAllAssociative();
        if (empty($fossils)) {
            return [];
        }

        $fossils = $this->prepareListResult($fossils);
        $fossilIds = $this->getFossilIds($fossils);

        $this->applyImages($fossilIds, $fossils);
        $this->applyTags($fossilIds, $fossils);
        $this->applyCategories($fossilIds, $fossils);

        return $fossils;
    }

    /**
     * @param array<FossilEntity> $fossils
     *
     * @return array<int,int>
     */
    private function getFossilIds(array $fossils): array
    {
        return array_map(function ($fossil) {
            return (int) $fossil->getId();
        }, $fossils);
    }

    public function getFossilListColumnCount(array $filter): int
    {
        $queryBuilder = $this->connection->createQueryBuilder()
            ->select(['COUNT(id)'])
            ->from(FossilRepositoryInterface::FOSSIL_TABLE_NAME)
            ->orderBy('id');

        $this->filterFactory->addFilter($filter, $queryBuilder);

        $result = $queryBuilder->executeQuery()->fetchOne();

        if (!is_numeric($result)) {
            throw new IsNotNumericException($this);
        }

        return (int) $result;
    }

    public function getColumnList(string $column): array
    {
        return $this->connection->createQueryBuilder()
            ->select([$column])
            ->distinct()
            ->from(self::FOSSIL_TABLE_NAME)
            ->executeQuery()
            ->fetchFirstColumn();
    }

    public function deleteFossil(int $fossilId): void
    {
        $this->connection->createQueryBuilder()
            ->delete(self::FOSSIL_TABLE_NAME)
            ->where('id = :id')
            ->setParameter('id', $fossilId)
            ->executeQuery();
    }

    public function getExportList(int $limit, int $offset): array
    {
        return $this->connection->createQueryBuilder()
            ->select(['*'])
            ->from(FossilRepositoryInterface::FOSSIL_TABLE_NAME)
            ->setFirstResult($offset)
            ->setMaxResults($limit)
            ->orderBy('id')
            ->executeQuery()
            ->fetchAllAssociative();
    }

    /**
     * @param array<int, array<string, mixed>> $result
     *
     * @return array<FossilEntity>
     */
    private function prepareListResult(array $result): array
    {
        $array = [];
        foreach ($result as $fossilFormField) {
            $array[] = (new FossilEntity())->fromArray($fossilFormField);
        }

        return $array;
    }

    private function createInsertUpdateQueryBuilder(FossilEntity $fossil, bool $isNewFossil): QueryBuilder
    {
        $queryBuilder = $this->connection->createQueryBuilder();
        if ($isNewFossil) {
            $function = self::INSERT_FUNCTION;
            $queryBuilder->insert(self::FOSSIL_TABLE_NAME);
        } else {
            $function = self::UPDATE_FUNCTION;
            $queryBuilder->update(self::FOSSIL_TABLE_NAME);
            $queryBuilder->where('id = :id')
                ->setParameter('id', $fossil->getId());
        }

        foreach ($this->fossilFormFieldRepository->getFossilFormFieldList() as $formField) {
            $fieldName = $formField->getFieldName();
            $getter = sprintf('get%s', ucfirst($fieldName));

            $queryBuilder->$function($fieldName, sprintf(':%s', $fieldName));
            $queryBuilder->setParameter($fieldName, $fossil->$getter());
        }

        return $queryBuilder;
    }

    // TODO: Rename function to saveRelationForCategoriesAndTags or something like that
    private function addCategoriesAndTags(FossilEntity $fossil): void
    {
        // TODO: Create a better solution for it. This is a hacky one. Check which exists and add only new ones.
        $this->deleteCategoriesAndTags($fossil->getId());

        $categoryAndTags = array_merge($fossil->getCategories(), $fossil->getTags());

        foreach ($categoryAndTags as $categoryOrTag) {
            $this->connection->createQueryBuilder()
                ->insert('tag_fossil')
                ->setValue('fossilId', ':fossilId')
                ->setValue('tagId', ':tagId')
                ->setParameter('fossilId', $fossil->getId())
                ->setParameter('tagId', (int) $categoryOrTag->getId())
                ->executeQuery();
        }
    }

    private function deleteCategoriesAndTags(?int $fossilId = null): void
    {
        if (!is_int($fossilId)) {
            return;
        }

        $this->connection->createQueryBuilder()
            ->delete('tag_fossil')
            ->where('fossilId = :fossilId')
            ->setParameter('fossilId', $fossilId)
            ->executeQuery();
    }

    /**
     * @param array<int,int>      $fossilIds
     * @param array<FossilEntity> $fossils
     */
    public function applyImages(array $fossilIds, array $fossils): void
    {
        $images = $this->imageRepository->getImagesForFossils($fossilIds);

        foreach ($fossils as $fossil) {
            $fossilImages = array_filter($images, function ($image) use ($fossil) {
                return $fossil->getId() === $image->getFossilId();
            });

            $fossil->setImages($fossilImages);
        }
    }

    /**
     * @param array<int,int>      $fossilIds
     * @param array<FossilEntity> $fossils
     */
    public function applyTags(array $fossilIds, array $fossils): void
    {
        $tags = $this->tagRepository->getByFossilIds($fossilIds, TagRepositoryInterface::GET_ONLY_TAGS);

        foreach ($fossils as $fossil) {
            if (array_key_exists((int) $fossil->getId(), $tags)) {
                $fossil->setTags($tags[$fossil->getId()]);
            }
        }
    }

    /**
     * @param array<int,int>      $fossilIds
     * @param array<FossilEntity> $fossils
     */
    public function applyCategories(array $fossilIds, array $fossils): void
    {
        $categories = $this->tagRepository->getByFossilIds($fossilIds, TagRepositoryInterface::GET_ONLY_CATEGORIES);

        foreach ($fossils as $fossil) {
            if (array_key_exists((int) $fossil->getId(), $categories)) {
                $fossil->setTags($categories[$fossil->getId()]);
            }
        }
    }
}