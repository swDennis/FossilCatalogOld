<?php

namespace App\Import;

use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\Request;

interface ImportUploadServiceInterface
{
    public const IMPORT_DIRECTORY = 'public/import';

    public function moveToImportDirectory(Request $request, string $formName, string $fileKey): File;
}