<?php

namespace App\Import\Handler;

use App\Import\ImportStatus;
use App\Repository\ImageRepositoryInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpKernel\KernelInterface;

abstract class AbstractImportHandler
{
    public const IMPORT_LIMIT = 20;

    protected ImportStatus $status;

    public function __construct(
        private readonly RequestStack $requestStack
    ) {}

    abstract public function import(): ImportStatus;

    abstract public function getKey(): string;

    abstract public function getFileName(): string;

    public function analyzeData(string $directory): ImportStatus
    {
        $sourceFile = $this->createFullPath($directory, $this->getFileName());

        $toImport = $this->getNumberOfLines($sourceFile);

        $this->status = new ImportStatus($this->getKey(), $sourceFile, true, false, $toImport);

        $this->saveSession();

        return $this->status;
    }

    public function clearSession(): void
    {
        $this->requestStack->getSession()->remove($this->getKey());
    }

    public function getStatus(): ImportStatus
    {
        return $this->getStatusFromSession();
    }

    protected function getStatusFromSession(): ImportStatus
    {
        $array = $this->requestStack->getSession()->get($this->getKey(), []);
        if (!is_array($array)) {
            throw new \UnexpectedValueException('Expect array got ' . gettype($array));
        }

        return (new ImportStatus($this->getKey(), ''))->fromArray($array);
    }

    protected function saveSession(): void
    {
        $this->requestStack->getSession()->set($this->getKey(), $this->status->toArray());
    }


    protected function createFullPath(string $directory, string $fileName): string
    {
        return sprintf('%s/%s', $directory, $fileName);
    }

    protected function getNumberOfLines(string $importFile): int
    {
        $linecount = 0;
        $file = fopen($importFile, 'rb');
        if (!$file) {
            throw new \UnexpectedValueException('Expects file for line count got ' . gettype($file));
        }

        while (!feof($file)) {
            $line = fgets($file);
            if (!$line) {
                break;
            }

            $linecount++;
        }

        fclose($file);

        return $linecount;
    }
}