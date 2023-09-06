<?php

namespace App\Import\Handler;

use App\Entity\FossilFormField;
use App\Import\ImportStatus;
use App\Repository\FossilFormFieldRepositoryInterface;
use App\Services\FossilForm\FossilFormEntityCreator;
use App\Services\FossilForm\FossilFormEntityDatabaseCreator;
use Symfony\Component\HttpFoundation\RequestStack;

class ImportFossilFormFieldHandler extends AbstractImportHandler
{
    public const IMPORT_LIMIT = PHP_INT_MAX;

    public function __construct(
        private readonly RequestStack $requestStack,
        private readonly FossilFormFieldRepositoryInterface $fossilFormFieldRepository,
        private readonly FossilFormEntityCreator $fossilFormEntityCreator,
        private readonly FossilFormEntityDatabaseCreator $fossilFormEntityDatabaseCreator
    ) {
        parent::__construct($this->requestStack);
    }

    public function getKey(): string
    {
        return 'fossilFormFieldStatus';
    }

    public function getFileName(): string
    {
        return 'FossilFormField.csv';
    }

    public function import(): ImportStatus
    {
        $this->status = $this->getStatusFromSession();

        $file = fopen($this->status->getFile(), 'rb');

        $lineCounter = 0;
        for ($i = 0; $i < self::IMPORT_LIMIT; $i++) {
            $line = fgets($file);
            if (empty($line)) {
                break;
            }

            $array = explode(',', $line);

            $formField = new FossilFormField();
            $formField->fromArray([
                'id' => $array[0],
                'fieldOrder' => $array[1],
                'fieldName' => $array[2],
                'fieldLabel' => $array[3],
                'fieldType' => $array[4],
                'showInOverview' => (bool) $array[5],
                'allowBlank' => (bool) $array[6],
                'isFilter' => (bool) $array[7],
                'isRequiredDefault' => (bool) preg_replace('/[\x00-\x1F\x7F]/u', '', $array[8]),
            ]);

            $this->fossilFormFieldRepository->saveFossilFormField($formField, true);

            $lineCounter++;
        }

        fclose($file);

        $this->status->add($lineCounter);

        if ($this->status->getImported() >= $this->status->getToImport()) {
            $this->status->finish();
        }

        $this->fossilFormEntityCreator->createFossilFormEntity();
        $this->fossilFormEntityDatabaseCreator->addDatabaseColumns();

        $this->saveSession();

        return $this->status;
    }
}