<?php

namespace App\Export\Handler;

use App\Export\ExportStatus;
use Symfony\Component\HttpFoundation\RequestStack;

abstract class AbstractHandler
{
    protected string $targetFile;

    public function __construct(
        private readonly RequestStack $requestStack
    ) {}

    abstract public function getKey(): string;

    abstract public function getFileName(): string;

    abstract public function analyzeData(): ExportStatus;

    abstract public function export(): ExportStatus;

    public function getStatus(): ExportStatus
    {
        return $this->getStatusFromSession();
    }

    public function initialize(string $targetDirectory): void
    {
        $session = $this->requestStack->getSession();
        $sessionFileNameKey = $this->getSessionFileNameKey();

        $targetFile = $session->get($sessionFileNameKey, false);
        if (is_string($targetFile)) {
            $this->targetFile = $targetFile;

            return;
        }

        $this->targetFile = sprintf('%s/%s', $targetDirectory, $this->getFileName());

        $session->set($sessionFileNameKey, $this->targetFile);
    }

    public function setFile(string $file): void
    {
        $this->targetFile = $file;
    }

    public function clearSession(): void
    {
        $emptyStatus = $this->analyzeData();
        $session = $this->requestStack->getSession();

        $session->set($this->getKey(), $emptyStatus->toArray());
        $session->remove($this->getSessionFileNameKey());
    }

    protected function saveSession(ExportStatus $abstractStatus): void
    {
        $this->requestStack->getSession()->set($this->getKey(), $abstractStatus->toArray());
    }

    protected function getStatusFromSession(): ExportStatus
    {
        $sessionStatus = $this->requestStack->getSession()->get($this->getKey(), []);
        if (!is_array($sessionStatus)) {
            throw new \UnexpectedValueException('Expect array to create new ExportStatus, got ' . gettype($sessionStatus));
        }

        return (new ExportStatus($this->getKey()))->fromArray($sessionStatus);
    }

    private function getSessionFileNameKey(): string
    {
        return $this->getKey() . 'FileName';
    }
}