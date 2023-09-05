<?php

namespace App\Services\Filter\FilterQueryHandler;

use Doctrine\DBAL\ArrayParameterType;
use Doctrine\DBAL\Query\QueryBuilder;

class FilterQueryHandler extends AbstractArrayQueryFilter implements FilterQueryHandlerInterface
{
    private string $filterName;

    public function __construct(string $filterName)
    {
        $this->filterName = $filterName;
    }

    public function addFilter(array $filter, QueryBuilder $queryBuilder): void
    {
        if (!array_key_exists($this->filterName, $filter) || !is_array($filter[$this->filterName])) {
            return;
        }

        $tagFilter = $this->filter($filter[$this->filterName]);
        if (empty($tagFilter)) {
            return;
        }

        $queryBuilder
            ->andWhere($this->filterName . ' IN (:filterValue)')
            ->setParameter('filterValue', $filter[$this->filterName], ArrayParameterType::STRING);
    }
}