<?php

namespace App\Form\FormEntities;

class Images
{
    protected ?int $fossilId = null;

    /**
     * @var array<mixed>
     */
    protected array $images = [];

    /**
     * @return int|null
     */
    public function getFossilId(): ?int
    {
        return $this->fossilId;
    }

    /**
     * @param int|null $fossilId
     */
    public function setFossilId(?int $fossilId): void
    {
        $this->fossilId = $fossilId;
    }

    /**
     * @return array<mixed>
     */
    public function getImages(): array
    {
        return $this->images;
    }

    /**
     * @param array<mixed> $images
     */
    public function setImages(array $images): void
    {
        $this->images = $images;
    }
}