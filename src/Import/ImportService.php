<?php

namespace App\Import;

use App\Export\Handler\AbstractHandler;
use App\Import\Handler\ImportCategoryHandler;
use App\Import\Handler\ImportFossilFormFieldHandler;
use App\Import\Handler\ImportImagesHandler;
use App\Import\Handler\ImportTagCategoryRelationHandler;
use App\Import\Handler\ImportTagHandler;

class ImportService implements ImportServiceInterface
{
    private array $handler;

    public function __construct(
        private readonly ImportTagHandler $importTagHandler,
        private readonly ImportCategoryHandler $importCategoryHandler,
        private readonly ImportTagCategoryRelationHandler $tagCategoryRelationHandler,
        private readonly ImportFossilFormFieldHandler $fossilFormFieldHandler,
        private readonly ImportImagesHandler $importImagesHandler
    ) {
        $this->handler = [
            $this->importTagHandler,
            $this->importCategoryHandler,
            $this->tagCategoryRelationHandler,
            $this->fossilFormFieldHandler,
            $this->importImagesHandler,
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
