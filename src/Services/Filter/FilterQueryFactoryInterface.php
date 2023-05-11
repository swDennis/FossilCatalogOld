<?php

namespace App\Services\Filter;

use Doctrine\DBAL\Query\QueryBuilder;

interface FilterQueryFactoryInterface
{
    public function addFilter(array $filter, QueryBuilder $queryBuilder): void;
}