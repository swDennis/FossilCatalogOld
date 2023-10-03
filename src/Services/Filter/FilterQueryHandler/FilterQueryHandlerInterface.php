<?php

namespace App\Services\Filter\FilterQueryHandler;

use Doctrine\DBAL\Query\QueryBuilder;

interface FilterQueryHandlerInterface
{
    /**
     * @param array<mixed> $filter
     */
    public function addFilter(array $filter, QueryBuilder $queryBuilder): void;
}