<?php

namespace App\Services\Filter\FilterQueryHandler;

use Doctrine\DBAL\ArrayParameterType;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Query\QueryBuilder;

class TagQueryFilterQuery extends AbstractArrayQueryFilter implements FilterQueryHandlerInterface
{
    public function __construct(
        private readonly Connection $connection
    ) {}

    private const FILTER_NAME = 'tags';

    /**
     * @param array<array<int>> $filter
     */
    public function addFilter(array $filter, QueryBuilder $queryBuilder): void
    {
        if (!array_key_exists(self::FILTER_NAME, $filter) || !is_array($filter[self::FILTER_NAME])) {
            return;
        }

        $tagFilter = $this->filter($filter[self::FILTER_NAME]);
        if (empty($tagFilter)) {
            return;
        }

        $fossilIds = $this->connection->createQueryBuilder()
            ->select(['fossilId'])
            ->from('tag_fossil')
            ->where('tagId IN (:tagIds)')
            ->setParameter('tagIds', $tagFilter, ArrayParameterType::INTEGER)
            ->executeQuery()
            ->fetchFirstColumn();

        $queryBuilder->andWhere('id IN (:fossilIdsInTags)')
            ->setParameter('fossilIdsInTags', $fossilIds, ArrayParameterType::INTEGER);
    }
}