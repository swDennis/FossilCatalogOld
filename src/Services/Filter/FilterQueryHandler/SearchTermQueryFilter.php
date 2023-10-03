<?php

namespace App\Services\Filter\FilterQueryHandler;

use App\Repository\FossilFormFieldRepositoryInterface;
use Doctrine\DBAL\Query\QueryBuilder;

class SearchTermQueryFilter implements FilterQueryHandlerInterface
{
    private const FILTER_NAME = 'searchTerm';

    public function __construct(
        private readonly FossilFormFieldRepositoryInterface $fossilFormFieldRepository
    ) {

    }

    /**
     * @param array<string,string> $filter
     */
    public function addFilter(array $filter, QueryBuilder $queryBuilder): void
    {
        if (!array_key_exists(self::FILTER_NAME, $filter) || empty($filter[self::FILTER_NAME])) {
            return;
        }

        $filterFields = $this->fossilFormFieldRepository->getFilterableFields();

        if (array_key_exists('searchTerm', $filter) && !empty(trim($filter['searchTerm']))) {
            $searchTerm = str_replace('___', trim($filter['searchTerm']), '%___%');
            $queryBuilder->setParameter('searchTerm', $searchTerm);

            $isFirst = true;
            foreach ($filterFields as $filterField) {
                if ($isFirst) {
                    $queryBuilder->andWhere($filterField->getFieldName() . ' LIKE :searchTerm');
                    $isFirst = false;
                }

                $queryBuilder->orWhere($filterField->getFieldName() . ' LIKE :searchTerm');
            }

            $queryBuilder->setParameter('searchTerm', $searchTerm);
        }
    }
}