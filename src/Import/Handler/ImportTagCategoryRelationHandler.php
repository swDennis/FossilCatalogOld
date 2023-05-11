<?php

namespace App\Import\Handler;

use App\Import\ImportStatus;
use App\Repository\TagCategoryRelationRepositoryInterface;
use Symfony\Component\HttpFoundation\RequestStack;

class ImportTagCategoryRelationHandler extends AbstractImportHandler
{
    public function __construct(
        private readonly RequestStack $requestStack,
        private readonly TagCategoryRelationRepositoryInterface $tagCategoryRelationRepository
    ) {
        parent::__construct($this->requestStack);
    }

    public function getKey(): string
    {
        return 'tagCategoryRelationStatus';
    }

    public function getFileName(): string
    {
        return 'TagCategoryRelation.csv';
    }

    public function import(): ImportStatus
    {
        $this->status = $this->getStatusFromSession();

        $file = fopen($this->status->getFile(), 'rb');

        $offset = $this->status->getImported();
        for ($i = 0; $i < $offset; $i++) {
            fgets($file);
        }

        $lineCounter = 0;
        for ($i = 0; $i < self::IMPORT_LIMIT; $i++) {
            $line = fgets($file);
            if (empty($line)) {
                break;
            }

            $array = explode(',', $line);

            $this->tagCategoryRelationRepository->import([
                'id' => $array[0],
                'tagId' => $array[1],
                'fossilId' => preg_replace('/[\x00-\x1F\x7F]/u', '', $array[2]),
            ]);


            $lineCounter++;
        }

        fclose($file);

        $this->status->add($lineCounter);

        if ($this->status->getImported() >= $this->status->getToImport()) {
            $this->status->finish();
        }

        $this->saveSession();

        return $this->status;
    }
}