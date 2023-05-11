<?php

namespace App\Services\Filter;

use App\Repository\FossilFormFieldRepositoryInterface;
use App\Services\Filter\FilterQueryHandler\CategoryQueryFilter;
use App\Services\Filter\FilterQueryHandler\FilterQueryHandlerInterface;
use App\Services\Filter\FilterQueryHandler\SearchTermQueryFilter;
use App\Services\Filter\FilterQueryHandler\TagQueryFilterQuery;
use Doctrine\DBAL\Query\QueryBuilder;

class FilterQueryQueryFactory implements FilterQueryFactoryInterface
{
    /**
     * @var array<int,FilterQueryHandlerInterface>
     */
    private array $handlers;

    public function __construct(
        private readonly SearchTermQueryFilter $searchTermFilter,
        private readonly CategoryQueryFilter $categorieFilter,
        private readonly TagQueryFilterQuery $tagFilter,
        private readonly FossilFormFieldRepositoryInterface $fossilFormFieldRepository
    )
    {
        $this->handlers = [
            'searchTerm' => $this->searchTermFilter,
            'categories' => $this->categorieFilter,
            'tags' => $this->tagFilter,
        ];
    }

    public function addFilter(array $filter, QueryBuilder $queryBuilder): void
    {
        foreach ($filter as $filterColumn => $filterValues) {
            $handler = $this->getHandler($filterColumn);
            if ($handler === null) {
                continue;
            }

            $handler->addFilter($filter, $queryBuilder);
        }
    }

    private function getHandler(string $name): ?FilterQueryHandlerInterface
    {
        if (!array_key_exists($name, $this->handlers)) {
            return null;
        }

        return $this->handlers[$name];
    }
}