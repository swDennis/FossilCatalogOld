<?php

namespace App\Import;

interface ImportServiceInterface
{
    public function analyzeData(string $directory): array;

    public function import(): array;

    public function clearSession(): void;
}