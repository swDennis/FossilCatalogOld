<?php

namespace App\Import\Handler;

use App\Entity\FossilEntity;
use App\Import\ImportStatus;
use App\Repository\FossilFormFieldRepositoryInterface;
use App\Repository\FossilRepositoryInterface;
use Symfony\Component\HttpFoundation\RequestStack;

class ImportFossilHandler extends AbstractImportHandler
{
    public function __construct(
        private readonly RequestStack                       $requestStack,
        private readonly FossilRepositoryInterface          $fossilRepository,
        private readonly FossilFormFieldRepositoryInterface $fossilFormFieldRepository
    ) {
        parent::__construct($this->requestStack);
    }

    public function getKey(): string
    {
        return 'fossilStatus';
    }

    public function getFileName(): string
    {
        return 'Fossil.csv';
    }

    public function import(): ImportStatus
    {
        $this->status = $this->getStatusFromSession();

        $file = fopen($this->status->getFile(), 'rb');

        $offset = $this->status->getImported();
        for ($i = 0; $i < $offset; $i++) {
            fgets($file);
        }

        $fields = $this->fossilFormFieldRepository->getFossilFormFieldList();

        $lineCounter = 0;
        for ($i = 0; $i < self::IMPORT_LIMIT; $i++) {
            $line = fgets($file);
            if (empty($line)) {
                break;
            }

            $array = explode(',', $line);
            $fossilArray = [
                'id' => $array[0],
            ];

            $index = 1;
            foreach ($fields as $field) {
                $fossilArray[$field['fieldName']] = $array[$index];
                ++$index;
            }

            $fossil = new FossilEntity();
            $fossil->fromArray($fossilArray);

            $this->fossilRepository->saveFossil($fossil, true);

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