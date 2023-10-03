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
        if (!$file) {
            throw new \UnexpectedValueException('Expects file to import form fields');
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

            $formField = new FossilFormField();
            $formField->fromArray($array);

            $exists = empty($this->fossilFormFieldRepository->getFossilFormFieldById((int) $formField->getId()));

            /** @phpstan-ignore-next-line */
            $this->fossilFormFieldRepository->saveFossilFormField($formField, $exists);

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