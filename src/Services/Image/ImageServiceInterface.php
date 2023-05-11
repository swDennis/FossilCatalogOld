<?php

namespace App\Services\Image;

use App\Services\Image\TransportObjects\ImageNames;
use App\Services\Image\TransportObjects\ImagePath;
use Symfony\Component\HttpFoundation\Request;

interface ImageServiceInterface
{
    public const IMAGE_ARRAY_KEY = 'images';

    public const BASE_PATH = 'images/gallery';

    public const IMAGE_NAME_TEMPLATE = '%s_%s_%s';

    public const THUMBNAIL_NAME_TEMPLATE = '%s_%s_thumbnail_%s';

    public const PATH_TEMPLATE = '%s/%s';

    public const ALGORYTHM = 'sha256';

    public const NOW = 'now';

    public const CHUNK_STRING_LENGTH = 5;

    public const ARRAY_SLICE_OFFSET = 0;

    public const ARRAY_SLICE_LENGTH = 3;

    public const RANDOM_BYTE_LENGTH = 2;

    public function saveImagesFromRequest(
        Request $request,
        string $formName,
        int $fossilId,
        bool $isNewFossil = false
    ): array;
}


