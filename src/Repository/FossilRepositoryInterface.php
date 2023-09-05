<?php

namespace App\Repository;

use App\Entity\FossilEntity;

interface FossilRepositoryInterface
{
    public const FOSSILS_PER_PAGE = 25;

    public const FOSSIL_TABLE_NAME = 'fossil_entity';
    public const FOSSIL_COLUMN_NUMBER = 'number';
    public const FOSSIL_COLUMN_NAME_GENIUS = 'nameGenus';
    public const FOSSIL_COLUMN_NAME_SPECIES = 'nameSpecies';
    public const FOSSIL_COLUMN_PLACE_OF_DISCOVERY_NAME = 'placeOfDiscoveryName';
    public const FOSSIL_COLUMN_PLACE_OF_DISCOVERY_COUNTRY = 'placeOfDiscoveryCountry';
    public const FOSSIL_COLUMN_PLACE_OF_DISCOVERY_CONTINENT = 'placeOfDiscoveryContinent';
    public const FOSSIL_COLUMN_FIND_LAYER_FORMATION = 'findLayerFormation';
    public const FOSSIL_COLUMN_FIND_LAYER_MEMBER = 'findLayerMember';
    public const FOSSIL_COLUMN_PALEOZOIC_SYSTEM = 'palaeozoicSystem';
    public const FOSSIL_COLUMN_PALEOZOIC_SERIES = 'palaeozoicSeries';
    public const FOSSIL_COLUMN_PALEOZOIC_STAGE = 'palaeozoicStage';

    public function saveFossil(FossilEntity $fossil): FossilEntity;

    public function getFossilById(int $id): array;

    public function getFossilList(array $filter): array;

    public function getExportList(int $limit, int $offset): array;

    public function getFossilListColumnCount(array $filter): int;

    public function getColumnList(string $column): array;

    public function deleteFossil(int $fossilId): void;
}