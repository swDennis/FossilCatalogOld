<?php

namespace App\Services\Filter\FilterQueryHandler;

use Doctrine\DBAL\Query\QueryBuilder;

interface FilterQueryHandlerInterface
{
    public function addFilter(array $filter, QueryBuilder $queryBuilder): void;
}