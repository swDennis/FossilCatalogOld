<?php

namespace App\Repository;

use Doctrine\DBAL\Connection;

interface TagCategoryRelationRepositoryInterface
{
    public const TABLE_NAME = 'tag_fossil';

    /**
     * @return array<array<string,mixed>>
     */
    public function getExportList(int $limit, int $offset): array;

    public function getColumnCount(): int;

    /**
     * @param array<string,int|string> $data
     */
    public function import(array $data): void;
}

