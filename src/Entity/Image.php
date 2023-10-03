<?php

namespace App\Entity;

class Image extends AbstractStruct
{
    protected ?int $id = null;

    protected ?int $fossilId = null;

    protected string $imageName;

    protected string $thumbnailName;

    protected string $mimeType;

    protected string $relativePath;

    protected string $relativeImagePath;

    protected string $relativeThumbnailPath;

    protected string $absolutePath;

    protected string $absoluteImagePath;

    protected string $absoluteThumbnailPath;

    protected bool $showInGallery = false;

    protected bool $isMainImage = false;

    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @param int|null $id
     */
    public function setId(?int $id): void
    {
        $this->id = $id;
    }

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

    public function getImageName(): string
    {
        return $this->imageName;
    }

    public function setImageName(string $imageName): void
    {
        $this->imageName = $imageName;
    }

    public function getThumbnailName(): string
    {
        return $this->thumbnailName;
    }

    public function setThumbnailName(string $thumbnailName): void
    {
        $this->thumbnailName = $thumbnailName;
    }

    public function getMimeType(): string
    {
        return $this->mimeType;
    }

    public function setMimeType(string $mimeType): void
    {
        $this->mimeType = $mimeType;
    }

    public function getRelativePath(): string
    {
        return $this->relativePath;
    }

    public function setRelativePath(string $relativePath): void
    {
        $this->relativePath = $relativePath;
    }

    public function getRelativeImagePath(): string
    {
        return $this->relativeImagePath;
    }

    public function setRelativeImagePath(string $relativeImagePath): void
    {
        $this->relativeImagePath = $relativeImagePath;
    }

    public function getRelativeThumbnailPath(): string
    {
        return $this->relativeThumbnailPath;
    }

    public function setRelativeThumbnailPath(string $relativeThumbnailPath): void
    {
        $this->relativeThumbnailPath = $relativeThumbnailPath;
    }

    public function getAbsolutePath(): string
    {
        return $this->absolutePath;
    }

    public function setAbsolutePath(string $absolutePath): void
    {
        $this->absolutePath = $absolutePath;
    }

    public function getAbsoluteImagePath(): string
    {
        return $this->absoluteImagePath;
    }

    public function setAbsoluteImagePath(string $absoluteImagePath): void
    {
        $this->absoluteImagePath = $absoluteImagePath;
    }

    public function getAbsoluteThumbnailPath(): string
    {
        return $this->absoluteThumbnailPath;
    }

    public function setAbsoluteThumbnailPath(string $absoluteThumbnailPath): void
    {
        $this->absoluteThumbnailPath = $absoluteThumbnailPath;
    }

    public function getShowInGallery(): bool
    {
        return $this->showInGallery;
    }

    public function setShowInGallery(bool $showInGallery): void
    {
        $this->showInGallery = $showInGallery;
    }

    public function getIsMainImage(): bool
    {
        return $this->isMainImage;
    }

    public function setIsMainImage(bool $isMainImage): void
    {
        $this->isMainImage = $isMainImage;
    }
}