<?php

namespace App\Export\Handler;

use App\Export\ExportStatus;
use App\Repository\TagCategoryRelationRepositoryInterface;
use App\Repository\TagRepositoryInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpKernel\KernelInterface;

class TagCategoryRelationExportHandler extends AbstractHandler
{
    private const EXPORT_LIMIT = 20;

    public function __construct(
        private readonly TagCategoryRelationRepositoryInterface $tagCategoryRelationRepository,
        private readonly RequestStack $requestStack
    ) {
        parent::__construct($this->requestStack);
    }

    public function analyzeData(): ExportStatus
    {
        $toExport = $this->tagCategoryRelationRepository->getColumnCount();

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

        $data = $this->tagCategoryRelationRepository->getExportList(self::EXPORT_LIMIT, $status->getExported());

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
        return 'tagCategoryRelationStatus';
    }

    public function getFileName(): string
    {
        return 'TagCategoryRelation.csv';
    }
}