<?php

namespace App\Entity;

class ImportEntity
{
    protected string $import;

    public function getImport(): string
    {
        return $this->import;
    }

    public function setImport(string $import): void
    {
        $this->import = $import;
    }
}