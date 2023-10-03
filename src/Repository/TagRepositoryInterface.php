<?php

namespace App\Repository;

use App\Entity\Tag;

interface TagRepositoryInterface
{
    public const GET_ONLY_CATEGORIES = 'getOnlyCategories';

    public const GET_ONLY_TAGS = 'getOnlyTags';

    public const GET_ALL = 'ALL';

    public const TABLE_NAME = 'tag';

    public const COLUMN_ID = 'id';

    public const COLUMN_NAME = 'name';

    public const COLUMN_IS_USED_AS_CATEGORY = 'isUsedAsCategory';

    public const NO_IDS = null;

    public function saveTag(Tag $tag): Tag;

    public function deleteTag(int $tagId): void;

    /**
     * @param array<int>|null $ids
     *
     * @return array<Tag>
     */
    public function getList(string $filter, ?array $ids): array;

    /**
     * @return array<int, array<string, mixed>>
     */
    public function getExportList(int $limit, int $offset, string $filter): array;

    public function getById(int $id): ?Tag;

    /**
     * @return array<Tag>
     */
    public function getByFossilId(int $fossilId, ?string $filter): array;

    /**
     * @param array<int> $fossilIds
     *
     * @return array<array<Tag>>
     */
    public function getByFossilIds(array $fossilIds, ?string $filter): array;

    public function getTagColumnCount(?string $filter): int;

    /**
     * @param array<array<string>>  $selected
     *
     * @return array<Tag>
     */
    public function getTagsThatAreAssignedToFossils(string $current, array $selected): array;
}