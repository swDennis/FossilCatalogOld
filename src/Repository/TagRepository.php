<?php

namespace App\Repository;

use App\Entity\Tag;
use Doctrine\DBAL\ArrayParameterType;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Query\QueryBuilder;

class TagRepository implements TagRepositoryInterface, RepositoryInterface
{
    public function __construct(public readonly Connection $connection)
    {
    }

    public function saveTag(Tag $tag, ?bool $isNew = null): Tag
    {
        if ($isNew === null) {
            $isNew = $tag->getId() === null;
        }

        $queryBuilder = $this->createInsertUpdateQueryBuilder($tag, $isNew);
        $queryBuilder->executeQuery();

        if (!$isNew) {
            return $tag;
        }

        $id = $this->connection->lastInsertId();
        if ($id === false) {
            throw new \RuntimeException('Could not create Tag entity');
        }

        $tag->setId($id);

        return $tag;
    }

    public function deleteTag(int $tagId): void
    {
        $this->connection->createQueryBuilder()
            ->delete('tag_fossil')
            ->where('tagId = :tagId')
            ->setParameter('tagId', $tagId)
            ->executeQuery();

        $this->connection->createQueryBuilder()
            ->delete(self::TABLE_NAME)
            ->where('id = :id')
            ->setParameter('id', $tagId)
            ->executeQuery();
    }

    public function getList(?string $filter = null, ?array $ids = null): array
    {
        $queryBuilder = $this->connection->createQueryBuilder()
            ->select(['*'])
            ->from(self::TABLE_NAME)
            ->orderBy(self::COLUMN_NAME, 'ASC');

        if (is_array($ids)) {
            $queryBuilder->andWhere('id IN (:ids)')
                ->setParameter('ids', $ids, ArrayParameterType::INTEGER);
        }

        if ($filter === self::GET_ALL) {
            $queryBuilder->addOrderBy(self::COLUMN_IS_USED_AS_CATEGORY, 'DESC');
        }

        if ($filter === self::GET_ONLY_CATEGORIES) {
            $queryBuilder->andWhere(self::COLUMN_IS_USED_AS_CATEGORY . ' =  1');
        }

        if ($filter === self::GET_ONLY_TAGS) {
            $queryBuilder->andWhere(self::COLUMN_IS_USED_AS_CATEGORY . ' =  0');
        }

        return $queryBuilder->executeQuery()->fetchAllAssociative();
    }

    public function getExportList(int $limit, int $offset, string $filter): array
    {
        $queryBuilder = $this->connection->createQueryBuilder()
            ->select(['*'])
            ->from(self::TABLE_NAME)
            ->setMaxResults($limit)
            ->setFirstResult($offset);

        if ($filter === self::GET_ONLY_CATEGORIES) {
            $queryBuilder->andWhere(self::COLUMN_IS_USED_AS_CATEGORY . ' =  1');
        }

        if ($filter === self::GET_ONLY_TAGS) {
            $queryBuilder->andWhere(self::COLUMN_IS_USED_AS_CATEGORY . ' =  0');
        }

        return $queryBuilder->executeQuery()->fetchAllAssociative();
    }

    public function getById(int $id): array
    {
        $result = $this->connection->createQueryBuilder()
            ->select(['*'])
            ->from(self::TABLE_NAME)
            ->where('id = :id')
            ->setParameter('id', $id)
            ->executeQuery()
            ->fetchAssociative();

        if (!is_array($result)) {
            return [];
        }

        return $result;
    }

    public function getByFossilId(int $fossilId, ?string $filter): array
    {
        return $this->getByFossilIds([$fossilId], $filter);
    }

    public function getByFossilIds(array $fossilIds, ?string $filter): array
    {
        $tagIds = $this->connection->createQueryBuilder()
            ->select(['tagId'])
            ->from('tag_fossil')
            ->where('fossilId IN (:fossilIds)')
            ->setParameter('fossilIds', $fossilIds, ArrayParameterType::INTEGER)
            ->executeQuery()
            ->fetchFirstColumn();

        $queryBuilder = $this->connection->createQueryBuilder()
            ->select(['*'])
            ->from(self::TABLE_NAME)
            ->where('id IN (:tagIds)')
            ->setParameter('tagIds', $tagIds, ArrayParameterType::INTEGER);

        if ($filter === self::GET_ONLY_CATEGORIES) {
            $queryBuilder->andWhere(self::COLUMN_IS_USED_AS_CATEGORY . ' =  1');
        }

        if ($filter === self::GET_ONLY_TAGS) {
            $queryBuilder->andWhere(self::COLUMN_IS_USED_AS_CATEGORY . ' =  0');
        }

        return $queryBuilder->executeQuery()->fetchAllAssociative();
    }

    public function getTagColumnCount(?string $filter): int
    {
        $queryBuilder = $this->connection->createQueryBuilder()
            ->select(['COUNT(id)'])
            ->from(self::TABLE_NAME);

        if ($filter === self::GET_ONLY_CATEGORIES) {
            $queryBuilder->andWhere(self::COLUMN_IS_USED_AS_CATEGORY . ' =  1');
        }

        if ($filter === self::GET_ONLY_TAGS) {
            $queryBuilder->andWhere(self::COLUMN_IS_USED_AS_CATEGORY . ' =  0');
        }

        return (int) $queryBuilder->executeQuery()->fetchOne();
    }

    public function getTagsThatAreAssignedToFossils(string $current, ?array $selected = null): array
    {
        $filter = $this->getFilter($current);

        if (empty($selected) || (count($selected) === 1 && empty($selected[0]))) {
            $selected = array_column($this->getList($filter), 'id');
        }

        $fossilIds = $this->connection->createQueryBuilder()
            ->select(['fossilId'])
            ->from('tag_fossil', 'tagFossil')
            ->join('tagFossil', self::TABLE_NAME, 'tag', 'tag.id = tagFossil.tagId')
            ->where('tag.id IN (:tagIds)')
            ->setParameter('tagIds', $selected, ArrayParameterType::INTEGER)
            ->executeQuery()
            ->fetchFirstColumn();

        $queryBuilder = $this->connection->createQueryBuilder()
            ->select(['DISTINCT tag.*'])
            ->from(self::TABLE_NAME, 'tag')
            ->join('tag', 'tag_fossil', 'tagFossil', 'tag.id = tagFossil.tagId')
            ->where('tagFossil.fossilId IN (:fossilIds)')
            ->setParameter('fossilIds', $fossilIds, ArrayParameterType::INTEGER);

        if ($filter === self::GET_ONLY_CATEGORIES) {
            $queryBuilder->andWhere(self::COLUMN_IS_USED_AS_CATEGORY . ' =  1');
        }

        if ($filter === self::GET_ONLY_TAGS) {
            $queryBuilder->andWhere(self::COLUMN_IS_USED_AS_CATEGORY . ' =  0');
        }

        return $queryBuilder->executeQuery()->fetchAllAssociative();
    }

    private function getFilter(string $current): ?string
    {
        switch ($current) {
            case 'categories':
            case self::GET_ONLY_TAGS:
                return self::GET_ONLY_TAGS;
            case 'tags':
            case self::GET_ONLY_CATEGORIES:
                return self::GET_ONLY_CATEGORIES;
            default:
                return self::GET_ALL;
        }
    }

    private function createInsertUpdateQueryBuilder(Tag $tag, bool $isNew): QueryBuilder
    {
        $queryBuilder = $this->connection->createQueryBuilder();

        if ($isNew) {
            $function = self::INSERT_FUNCTION;
            $queryBuilder->insert(self::TABLE_NAME);
        } else {
            $function = self::UPDATE_FUNCTION;
            $queryBuilder->update(self::TABLE_NAME);
            $queryBuilder->where('id = :id')
                ->setParameter('id', $tag->getId());
        }

        $queryBuilder->$function(self::COLUMN_NAME, ':tagName');
        $queryBuilder->$function(self::COLUMN_IS_USED_AS_CATEGORY, ':isUsedAsCategory');
        $queryBuilder->setParameter('tagName', $tag->getName())
            ->setParameter('isUsedAsCategory', (int) $tag->getIsUsedAsCategory());

        return $queryBuilder;

    }
}