<?php

namespace App\Import;

interface ImportServiceInterface
{
    /**
     * @return array<int|string, mixed>
     */
    public function analyzeData(string $directory): array;

    /**
     * @return array<int|string, mixed>
     */
    public function import(): array;

    public function clearSession(): void;
}