<?php

namespace App\Services\Filter\FilterQueryHandler;

abstract class AbstractArrayQueryFilter
{
    public function filter(array $filter): array
    {
        return array_filter($filter);
    }
}