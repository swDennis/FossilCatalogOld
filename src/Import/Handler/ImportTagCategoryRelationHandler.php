<?php

namespace App\Import\Handler;

use App\Import\ImportStatus;
use App\Repository\TagCategoryRelationRepositoryInterface;
use Symfony\Component\HttpFoundation\RequestStack;

class ImportTagCategoryRelationHandler extends AbstractImportHandler
{
    public function __construct(
        private readonly RequestStack                           $requestStack,
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
        if (!$file) {
            throw new \UnexpectedValueException('Expects file to import relations');
        }

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

            $array = json_decode($line, true);
            if (!is_array($array)) {
                throw new \UnexpectedValueException('Expect array got ' . gettype($array));
            }
            $this->tagCategoryRelationRepository->import($array);

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