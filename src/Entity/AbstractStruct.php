<?php

namespace App\Entity;

/** @phpstan-ignore-next-line */
abstract class AbstractStruct implements \ArrayAccess
{
    /**
     * @param array<string, mixed> $data
     */
    public function fromArray(array $data): static
    {
        foreach ($data as $key => $value) {
            $this->$key = $value;
        }

        return $this;
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return (array) $this;
    }

    public function offsetExists(mixed $offset): bool
    {
        return $this->$offset !== null;
    }

    public function offsetGet(mixed $offset): mixed
    {
        return $this->$offset;
    }

    public function offsetSet(mixed $offset, mixed $value): void
    {
        $this->$offset = $value;
    }

    public function offsetUnset(mixed $offset): void
    {
        $this->$offset = null;
    }
}