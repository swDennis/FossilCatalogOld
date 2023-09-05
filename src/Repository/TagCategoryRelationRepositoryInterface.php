<?php

namespace App\Repository;

use Doctrine\DBAL\Connection;

interface TagCategoryRelationRepositoryInterface
{
    public const TABLE_NAME = 'tag_fossil';

    public function getExportList(int $limit, int $offset): array;

    public function getColumnCount(): int;

    public function import(array $data): void;
}

