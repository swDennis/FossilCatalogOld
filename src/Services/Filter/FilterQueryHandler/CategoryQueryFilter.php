<?php

namespace App\Services\Filter\FilterQueryHandler;

use Doctrine\DBAL\ArrayParameterType;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Query\QueryBuilder;

class CategoryQueryFilter extends AbstractArrayQueryFilter implements FilterQueryHandlerInterface
{
    public function __construct(
        private readonly Connection $connection
    ) {

    }

    private const FILTER_NAME = 'categories';

    public function addFilter(array $filter, QueryBuilder $queryBuilder): void
    {
        if (!array_key_exists(self::FILTER_NAME, $filter) || !is_array($filter[self::FILTER_NAME])) {
            return;
        }

        $categoryFilter = $this->filter($filter[self::FILTER_NAME]);
        if (empty($categoryFilter)) {
            return;
        }
        $fossilIds = $this->connection->createQueryBuilder()
            ->select(['fossilId'])
            ->from('tag_fossil')
            ->where('tagId IN (:tagIds)')
            ->setParameter('tagIds', $categoryFilter, ArrayParameterType::INTEGER)
            ->executeQuery()
            ->fetchFirstColumn();

        $queryBuilder->andWhere('id IN (:fossilIdsInCategories)')
            ->setParameter('fossilIdsInCategories', $fossilIds, ArrayParameterType::INTEGER);
    }
}