<?php

namespace App\Export;

use App\Exceptions\MissingArrayKeyException;

class ExportStatus
{
    protected string $type;

    protected bool $isFinished;

    protected int $toExport;

    protected int $exported;

    public function __construct(
        string $type,
        ?bool $isFinished = false,
        ?int $toExport = 0,
        ?int $exported = 0,
    ) {
        $this->type = $type;
        $this->isFinished = $isFinished;
        $this->toExport = $toExport;
        $this->exported = $exported;
    }

    public function add(int $count): void
    {
        $this->exported += $count;
    }

    public function finish(): void
    {
        $this->isFinished = true;
    }

    public function fromArray(array $data): ExportStatus
    {
        foreach ($this->getProperties() as $property) {
            if (!array_key_exists($property, $data)) {
                throw new MissingArrayKeyException($property, $data);
            }

            $this->$property = $data[$property];
        }

        return $this;
    }

    public function toArray(): array
    {
        $array = [];
        foreach ($this->getProperties() as $property) {
            $array[$property] = $this->$property;
        }

        return $array;
    }

    public function getIsFinished(): bool
    {
        return $this->isFinished;
    }

    public function getToExport(): int
    {
        return $this->toExport;
    }

    public function getExported(): int
    {
        return $this->exported;
    }

    private function getProperties(): array
    {
        return [
            'type',
            'isFinished',
            'toExport',
            'exported',
        ];
    }
}