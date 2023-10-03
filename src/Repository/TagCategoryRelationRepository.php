<?php

namespace App\Repository;

use App\Exceptions\IsNotNumericException;
use Doctrine\DBAL\Connection;

class TagCategoryRelationRepository implements TagCategoryRelationRepositoryInterface
{
    public function __construct(public readonly Connection $connection) {}


    public function getExportList(int $limit, int $offset): array
    {
        return $this->connection->createQueryBuilder()
            ->select(['*'])
            ->from(self::TABLE_NAME)
            ->setMaxResults($limit)
            ->setFirstResult($offset)
            ->executeQuery()
            ->fetchAllAssociative();
    }

    public function getColumnCount(): int
    {
        /** @phpstan-ignore-next-line */
        return $this->connection->createQueryBuilder()
            ->select(['COUNT(id)'])
            ->from(self::TABLE_NAME)
            ->executeQuery()
            ->fetchOne();
    }

    public function import(array $data): void
    {
        $this->connection->createQueryBuilder()
            ->insert(self::TABLE_NAME)
            ->setValue('id', ':id')
            ->setValue('tagId', ':tagId')
            ->setValue('fossilId', ':fossilId')
            ->setParameter('id', $data['id'])
            ->setParameter('tagId', $data['tagId'])
            ->setParameter('fossilId', $data['fossilId'])
            ->executeQuery();
    }
}

