<?php

namespace App\Services\Filter;

use Doctrine\DBAL\Query\QueryBuilder;

interface FilterQueryFactoryInterface
{
    /**
     * @param array<mixed> $filter
     */
    public function addFilter(array $filter, QueryBuilder $queryBuilder): void;
}