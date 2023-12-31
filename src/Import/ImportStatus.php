<?php

namespace App\Import;

use App\Exceptions\MissingArrayKeyException;

class ImportStatus
{
    protected string $type;

    private string $file;

    private ?bool $isAnalyzed;

    protected bool $isFinished;

    protected int $toImport;

    protected int $imported;

    public function __construct(
        string  $type,
        ?string $file = '',
        ?bool   $isAnalyzed = false,
        ?bool   $isFinished = false,
        ?int    $toImport = 0,
        ?int    $imported = 0,
    ) {
        $this->type = $type;
        $this->file = $file ?? '';
        $this->isAnalyzed = $isAnalyzed ?? false;
        $this->isFinished = $isFinished ?? false;
        $this->toImport = $toImport ?? 0;
        $this->imported = $imported ?? 0;
    }

    public function add(int $count): void
    {
        $this->imported += $count;
    }

    public function finish(): void
    {
        $this->isFinished = true;
    }

    /**
     * @return array<string,mixed>
     */
    public function toArray(): array
    {
        $array = [];
        foreach ($this->getProperties() as $property) {
            $array[$property] = $this->$property;
        }

        return $array;
    }

    /**
     * @param array<string,mixed> $data
     */
    public function fromArray(array $data): ImportStatus
    {
        if (empty($data)) {
            return $this;
        }

        foreach ($this->getProperties() as $property) {
            if (!array_key_exists($property, $data)) {
                throw new MissingArrayKeyException($property);
            }

            $this->$property = $data[$property];
        }

        return $this;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getFile(): string
    {
        return $this->file;
    }

    public function getIsFinished(): bool
    {
        return $this->isFinished;
    }

    public function getIsAnalyzed(): ?bool
    {
        return $this->isAnalyzed;
    }

    public function getToImport(): int
    {
        return $this->toImport;
    }

    public function getImported(): int
    {
        return $this->imported;
    }

    public function hasFile(): bool
    {
        return is_file($this->file);
    }

    /**
     * @return array<int, string>
     */
    private function getProperties(): array
    {
        return [
            'type',
            'file',
            'isFinished',
            'toImport',
            'imported',
        ];
    }
}