<?php

namespace App\Export;

use App\Export\Exceptions\CreateZipException;
use App\Export\Handler\AbstractHandler;
use App\Export\Handler\CategoryExportHandler;
use App\Export\Handler\FossilExportHandler;
use App\Export\Handler\FossilFormFieldsExportHandler;
use App\Export\Handler\ImagesExportHandler;
use App\Export\Handler\TagCategoryRelationExportHandler;
use App\Export\Handler\TagExportHandler;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpKernel\KernelInterface;
use ZipArchive;

class ExportService implements ExportServiceInterface
{
    private const LOCK_FILE_SESSION_KEY = 'exportLockFile';

    private const TARGET_DIRECTORY_SESSION_KEY = 'exportTargetDirectory';

    protected string $targetDirectory;

    protected string $lockFile;

    private array $handler;

    public function __construct(
        private readonly RequestStack $requestStack,
        private readonly KernelInterface $appKernel,
        private readonly TagExportHandler $tagExportHandler,
        private readonly CategoryExportHandler $categoryExportHandler,
        private readonly FossilExportHandler $fossilExportHandler,
        private readonly TagCategoryRelationExportHandler $tagCategoryRelationExportHandler,
        private readonly FossilFormFieldsExportHandler $fossilFormFieldsExportHandler,
        private readonly ImagesExportHandler $imagesExportHandler
    ) {
        $this->handler = [
            $this->tagExportHandler,
            $this->categoryExportHandler,
            $this->fossilExportHandler,
            $this->tagCategoryRelationExportHandler,
            $this->fossilFormFieldsExportHandler,
            $this->imagesExportHandler,
        ];
    }

    public function analyzeData(): array
    {
        $status = [];

        /** @var AbstractHandler $exportHandler */
        foreach ($this->handler as $exportHandler) {
            $status[$exportHandler->getKey()] = $exportHandler->analyzeData()->toArray();
        }

        return $status;
    }

    public function export(): array
    {
        $status = [];

        /** @var AbstractHandler $exportHandler */
        foreach ($this->handler as $exportHandler) {
            if ($exportHandler->getStatus()->getIsFinished()) {
                continue;
            }

            $status[$exportHandler->getKey()] = $exportHandler->export()->toArray();
        }

        return $status;
    }

    public function initializeFiles(): void
    {
        $this->targetDirectory = $this->createTargetDirectory();
        $this->lockFile = $this->createLockFile($this->targetDirectory);

        /** @var AbstractHandler $exportHandler */
        foreach ($this->handler as $exportHandler) {
            $exportHandler->initialize($this->targetDirectory);
        }
    }

    public function clearSession(): void
    {
        /** @var AbstractHandler $exportHandler */
        foreach ($this->handler as $exportHandler) {
            $exportHandler->clearSession();
        }

        $this->targetDirectory = $this->createTargetDirectory();
        unlink($this->createLockFile($this->targetDirectory));
        $session = $this->requestStack->getSession();
        $session->remove(md5($this->targetDirectory));
        $session->remove(self::TARGET_DIRECTORY_SESSION_KEY);
    }

    public function createZipFile(string $directory, string $name): string
    {
        $target = $this->createFileName($directory, $name . self::ZIP_FILE_EXTENSION);

        $zip = new ZipArchive();
        if ($zip->open($target, ZipArchive::CREATE) !== true) {
            throw new CreateZipException('Cannot create zip file for backup');
        }

        /** @var AbstractHandler $exportHandler */
        foreach ($this->handler as $handler) {
            $handlerFile = $this->createFileName($directory, $handler->getFileName());
            if (!is_file($handlerFile)) {
                continue;
            }

            $zip->addFile($handlerFile, $handler->getFileName());
        }

        $zip->close();

        return $target;
    }

    public function deleteBackup(string $path): void
    {
        $filesystem = new Filesystem();
        $filesystem->remove($path);
    }

    private function createTargetDirectory(): string
    {
        $session = $this->requestStack->getSession();

        $targetDirectory = $session->get(self::TARGET_DIRECTORY_SESSION_KEY);
        if (is_string($targetDirectory)) {
            $this->createDirectory($targetDirectory);

            return $targetDirectory;
        }

        $targetDirectory = sprintf(
            '%s/%s/%s',
            $this->appKernel->getProjectDir(),
            'public/export',
            $this->getDateTimeString(),
        );

        $session->set(self::TARGET_DIRECTORY_SESSION_KEY, $targetDirectory);

        $this->createDirectory($targetDirectory);

        return $targetDirectory;
    }

    private function createLockFile(string $targetDirectory): string
    {
        $session = $this->requestStack->getSession();
        $lockFile = $session->get(md5($targetDirectory));
        if (is_string($lockFile)) {
            return $lockFile;
        }

        $lockFile = sprintf('%s/%s', $targetDirectory, 'in_progress.lock');

        $session->set(md5($targetDirectory), $lockFile);

        file_put_contents($lockFile, $this->getDateTimeString());

        return $lockFile;
    }

    private function createDirectory(string $directory)
    {
        if (is_dir($directory)) {
            return;
        }

        if (!mkdir($directory, 0777, true) && !is_dir($directory)) {
            throw new \RuntimeException(sprintf('Directory "%s" was not created', $directory));
        }
    }

    private function getDateTimeString(): string
    {
        return (new \DateTime())->format('y-m-d h:i:s');
    }

    private function createFileName(string $path, string $name): string
    {
        return sprintf('%s/%s', $path, $name);
    }
}
