<?php

namespace App\Repository;

interface ImportExportRepositoryInterface
{
    /**
     * @return array<array<string, bool|string>>
     */
    public function getExports(): array;
}