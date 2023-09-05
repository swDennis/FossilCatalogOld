<?php

namespace App\Form\FormEntities;

class Images
{
    protected ?int $fossilId = null;

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
     * @return array
     */
    public function getImages(): array
    {
        return $this->images;
    }

    /**
     * @param array $images
     */
    public function setImages(array $images): void
    {
        $this->images = $images;
    }
}