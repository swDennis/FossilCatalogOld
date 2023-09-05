<?php

namespace App\Export;

interface ExportServiceInterface
{
    const ZIP_FILE_EXTENSION = '.fossilienkatalog.backup.zip';

    public function analyzeData(): array;

    public function export(): array;

    public function initializeFiles(): void;

    public function clearSession(): void;

    public function createZipFile(string $directory, string $name): string;
}