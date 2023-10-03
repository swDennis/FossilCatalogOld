<?php

namespace App\Repository;

use App\Entity\FossilEntity;

interface FossilRepositoryInterface
{
    public const FOSSILS_PER_PAGE = 25;

    public const FOSSIL_TABLE_NAME = 'fossil_entity';

    public function saveFossil(FossilEntity $fossil): FossilEntity;

    public function getFossilById(int $id): ?FossilEntity;

    /**
     * @param array<string, mixed> $filter
     *
     * @return array<FossilEntity>
     */
    public function getFossilList(array $filter): array;

    /**
     * @param array<string, mixed> $filter
     */
    public function getFossilListColumnCount(array $filter): int;

    /**
     * @return array<int, mixed>
     */
    public function getColumnList(string $column): array;

    public function deleteFossil(int $fossilId): void;

    /**
     * @return array<int, array<string, mixed>>
     */
    public function getExportList(int $limit, int $offset): array;
}