<?php

namespace App\Repository;

use Symfony\Component\Finder\Finder;
use Symfony\Component\HttpKernel\KernelInterface;

class ImportExportRepository implements ImportExportRepositoryInterface
{
    private string $importExportDirectory;

    public function __construct(private readonly KernelInterface $appKernel)
    {
        $this->importExportDirectory = sprintf(
            '%s/%s',
            $this->appKernel->getProjectDir(),
            'public/export',
        );
    }

    public function getExports(): array
    {
        $finder = new Finder();

        $finder->directories()->in($this->importExportDirectory);

        if (!$finder->hasResults()) {
            return [];
        }

        $finder->sortByName()->reverseSorting();

        $exportArray = [];
        foreach ($finder as $directory) {
            $exportArray[] = [
                'name' => $directory->getRelativePathname(),
                'realPath'=> $directory->getRealPath(),
                'hasFinished' => !file_exists($directory->getRealPath() . '/in_progress.lock')
            ];
        }

        return $exportArray;
    }
}