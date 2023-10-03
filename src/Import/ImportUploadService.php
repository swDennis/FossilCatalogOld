<?php

namespace App\Import;

use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\KernelInterface;

class ImportUploadService implements ImportUploadServiceInterface
{
    public function __construct(
        private readonly KernelInterface $appKernel
    ) {}

    public function moveToImportDirectory(Request $request, string $formName, string $fileKey): File
    {
        $form = $request->files->get($formName);

        if (!is_array($form) || !array_key_exists($fileKey, $form)) {
            throw new \UnexpectedValueException(sprintf('Cannot access %s of form to get files', $fileKey));
        }

        /** @var UploadedFile $uploadedTmpFiles */
        $uploadedTmpFiles = $form[$fileKey];

        return $uploadedTmpFiles->move($this->createDirectoryPath(), $uploadedTmpFiles->getClientOriginalName());
    }

    private function createDirectoryPath(): string
    {
        return $this->connectPath($this->appKernel->getProjectDir(), self::IMPORT_DIRECTORY);
    }

    private function connectPath(string $basePath, string $fileName): string
    {
        return sprintf('%s/%s', $basePath, $fileName);
    }
}