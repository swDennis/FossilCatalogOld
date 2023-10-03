<?php

namespace App\Import\Handler;

use App\Entity\Tag;
use App\Import\ImportStatus;
use App\Repository\TagRepositoryInterface;
use Symfony\Component\HttpFoundation\RequestStack;

class ImportTagHandler extends AbstractImportHandler
{
    public function __construct(
        private readonly RequestStack           $requestStack,
        private readonly TagRepositoryInterface $tagRepository
    ) {
        parent::__construct($this->requestStack);
    }

    public function getKey(): string
    {
        return 'tagStatus';
    }

    public function getFileName(): string
    {
        return 'Tag.csv';
    }

    public function import(): ImportStatus
    {
        $this->status = $this->getStatusFromSession();

        $file = fopen($this->status->getFile(), 'rb');
        if (!$file) {
            throw new \UnexpectedValueException('Expects file to import tags');
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

            $tag = new Tag();
            $array = json_decode($line, true);
            if (!is_array($array)) {
                throw new \UnexpectedValueException('Expect array got ' . gettype($array));
            }

            $tag->fromArray($array);

            /** @phpstan-ignore-next-line */
            $this->tagRepository->saveTag($tag, true);

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