<?php

namespace App\Repository;

use App\Entity\Fossil;
use App\Entity\FossilEntity;
use App\Services\Filter\FilterQueryQueryFactory;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Query\QueryBuilder;

class FossilRepository implements FossilRepositoryInterface, RepositoryInterface
{


    public function __construct(
        private readonly Connection $connection,
        private readonly FilterQueryQueryFactory $filterFactory,
        private readonly FossilFormFieldRepositoryInterface $fossilFormFieldRepository,
        private readonly ImageRepositoryInterface $imageRepository,
        private readonly TagRepositoryInterface $tagRepository
    ) {
    }

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

        $fossil->setId($id);

        $this->addCategoriesAndTags($fossil);

        return $fossil;
    }

    public function getFossilById(int $id): array
    {
        $fossil = $this->connection->createQueryBuilder()
            ->select(['*'])
            ->from(FossilRepositoryInterface::FOSSIL_TABLE_NAME)
            ->where('id = :id')
            ->setParameter('id', $id)
            ->executeQuery()
            ->fetchAssociative();

        if (empty($fossil)) {
            return [];
        }

        $fossil['images'] = $this->imageRepository->getImagesByFossilId($fossil['id']);
        $fossil['categories'] = $this->tagRepository->getByFossilId($fossil['id'], TagRepositoryInterface::GET_ONLY_CATEGORIES);
        $fossil['tags'] = $this->tagRepository->getByFossilId($fossil['id'], TagRepositoryInterface::GET_ONLY_TAGS);

        return $fossil;
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

        // Filter search
        $this->filterFactory->addFilter($filter, $queryBuilder);

        $fossils = $queryBuilder->executeQuery()->fetchAllAssociative();
        $fossilIds = array_map(function ($fossil) {
            return $fossil['id'];
        }, $fossils);

        $images = $this->imageRepository->getImagesForFossils($fossilIds);

        foreach ($fossils as &$fossil) {
            $fossilImages = array_filter($images, function ($image) use ($fossil) {
                return $fossil['id'] === $image['fossilId'];
            });

            $fossil['images'] = $fossilImages;
        }

        return $fossils;
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

    public function getFossilListColumnCount(array $filter): int
    {
        $queryBuilder = $this->connection->createQueryBuilder()
            ->select(['COUNT(id)'])
            ->from(FossilRepositoryInterface::FOSSIL_TABLE_NAME)
            ->orderBy('id');

        $this->filterFactory->addFilter($filter, $queryBuilder);

        return $queryBuilder->executeQuery()
            ->fetchOne();
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
            $fieldName = $formField[FossilFormFieldRepositoryInterface::FORM_FIELD_COLUMN_FIELD_NAME];
            $getter = sprintf('get%s', ucfirst($fieldName));

            $queryBuilder->$function($fieldName, sprintf(':%s', $fieldName));
            $queryBuilder->setParameter($fieldName, $fossil->$getter());
        }

        return $queryBuilder;
    }

    private function addCategoriesAndTags(FossilEntity $fossil)
    {
        // TODO: Create a better solution for it. This is a hacky one.Check which exists and add only new ones.
        $this->deleteCategoriesAndTags($fossil->getId());

        $categoryAndTagIds = array_merge($fossil->getCategories(), $fossil->getTags());

        foreach ($categoryAndTagIds as $id) {
            $this->connection->createQueryBuilder()
                ->insert('tag_fossil')
                ->setValue('fossilId', ':fossilId')
                ->setValue('tagId', ':tagId')
                ->setParameter('fossilId', $fossil->getId())
                ->setParameter('tagId', (int) $id)
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

    private function applyFilter(QueryBuilder $queryBuilder, array $filter)
    {

    }


}