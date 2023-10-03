<?php

namespace App\Services\Filter\FilterQueryHandler;

abstract class AbstractArrayQueryFilter
{
    /**
     * @param array<mixed> $filter
     *
     * @return array<mixed>
     */
    public function filter(array $filter): array
    {
        return array_filter($filter);
    }
}