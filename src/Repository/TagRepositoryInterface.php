<?php

namespace App\Repository;

use App\Entity\Tag;

interface TagRepositoryInterface
{
    public const GET_ONLY_CATEGORIES = 'getOnlyCategories';

    public const GET_ONLY_TAGS = 'getOnlyTags';

    public const GET_ALL = null;

    public const TABLE_NAME = 'tag';

    public const COLUMN_ID = 'id';

    public const COLUMN_NAME = 'name';

    public const COLUMN_IS_USED_AS_CATEGORY = 'isUsedAsCategory';

    public function saveTag(Tag $tag): Tag;

    public function deleteTag(int $tagId): void;

    public function getList(?string $filter, ?array $ids): array;

    public function getExportList(int $limit, int $offset, string $filter): array;

    public function getById(int $id): array;

    public function getByFossilId(int $fossilId, ?string $filter): array;

    public function getByFossilIds(array $fossilIds, ?string $filter): array;

    public function getTagColumnCount(?string $filter): int;

    public function getTagsThatAreAssignedToFossils(string $current, array $selected): array;
}