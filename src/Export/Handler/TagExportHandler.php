<?php

namespace App\Export\Handler;

use App\Export\ExportStatus;
use App\Repository\TagRepositoryInterface;
use Symfony\Component\HttpFoundation\RequestStack;

class TagExportHandler extends AbstractHandler
{
    private const EXPORT_LIMIT = 20;

    public function __construct(
        private readonly TagRepositoryInterface $tagRepository,
        private readonly RequestStack $requestStack
    ) {
        parent::__construct($this->requestStack);
    }

    public function analyzeData(): ExportStatus
    {
        $toExport = $this->tagRepository->getTagColumnCount(TagRepositoryInterface::GET_ONLY_TAGS);

        $tagStatus = new ExportStatus($this->getKey(), false, $toExport);

        $this->saveSession($tagStatus);

        return $tagStatus;
    }

    public function export(): ExportStatus
    {
        $status = $this->getStatusFromSession();

        if ($status->getIsFinished()) {
            return $status;
        }

        $data = $this->tagRepository->getExportList(self::EXPORT_LIMIT, $status->getExported(), TagRepositoryInterface::GET_ONLY_TAGS);

        foreach ($data as $line) {
            \file_put_contents($this->targetFile, json_encode($line) . PHP_EOL, FILE_APPEND);
        }

        $status->add(count($data));

        if ($status->getExported() >= $status->getToExport()) {
            $status->finish();
        }

        $this->saveSession($status);

        return $status;
    }

    public function getKey(): string
    {
        return 'tagStatus';
    }

    public function getFileName(): string
    {
        return 'Tag.csv';
    }
}