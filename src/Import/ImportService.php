<?php

namespace App\Import;

use App\Import\Handler\ImportCategoryHandler;
use App\Import\Handler\ImportFossilFormFieldHandler;
use App\Import\Handler\ImportFossilHandler;
use App\Import\Handler\ImportImagesHandler;
use App\Import\Handler\ImportTagCategoryRelationHandler;
use App\Import\Handler\ImportTagHandler;

class ImportService implements ImportServiceInterface
{
    private array $handler;

    public function __construct(
        private readonly ImportFossilFormFieldHandler $fossilFormFieldHandler,
        private readonly ImportTagHandler $importTagHandler,
        private readonly ImportCategoryHandler $importCategoryHandler,
        private readonly ImportTagCategoryRelationHandler $tagCategoryRelationHandler,
        private readonly ImportImagesHandler $importImagesHandler,
        private readonly ImportFossilHandler $importFossilHandler
    ) {
        $this->handler = [
            $this->fossilFormFieldHandler,
            $this->importTagHandler,
            $this->importCategoryHandler,
            $this->tagCategoryRelationHandler,
            $this->importImagesHandler,
            $this->importFossilHandler,
        ];
    }

    public function analyzeData(string $directory): array
    {
        $status = [];

        foreach ($this->handler as $importHandler) {
            $status[$importHandler->getKey()] = $importHandler->analyzeData($directory)->toArray();
        }

        return $status;
    }

    public function import(): array
    {
        $status = [];

        foreach ($this->handler as $ImportHandler) {
            if ($ImportHandler->getStatus()->getIsFinished()) {
                continue;
            }

            $status[$ImportHandler->getKey()] = $ImportHandler->import()->toArray();
        }

        return $status;
    }

    public function clearSession(): void
    {
        foreach ($this->handler as $ImportHandler) {
            $ImportHandler->clearSession();
        }
    }
}
