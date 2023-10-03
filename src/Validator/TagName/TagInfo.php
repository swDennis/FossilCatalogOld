<?php

namespace App\Validator\TagName;

use App\Entity\Tag;
use App\Repository\TagRepositoryInterface;

class TagInfo
{
    private ?Tag $tag;

    /**
     * @param array<string,string|int>|null $postedTag
     */
    public function __construct(
        private readonly TagRepositoryInterface $tagRepository,
        private readonly ?int $requestUrlTagId,
        private readonly ?array $postedTag,
    ) {
        if (is_int($this->requestUrlTagId)) {
            $this->tag = $this->tagRepository->getById($this->requestUrlTagId);
        } elseif (is_array($this->postedTag) && array_key_exists('id', $this->postedTag) && !empty($this->postedTag['id'])) {
            $this->tag = $this->tagRepository->getById((int) $this->postedTag['id']);
        }
    }

    public function isValidationRequired(): bool
    {
        if ($this->requestUrlTagId !== null && empty($this->postedTag)) {
            return false;
        }

        if (!empty($this->postedTag) && $this->tag instanceof Tag) {
            if (strtolower(trim($this->tag->getName())) === strtolower(trim((string) $this->postedTag['name']))) {
                return false;
            }
        }

        return true;
    }
}