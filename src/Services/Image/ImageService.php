<?php

namespace App\Services\Image;

use App\Entity\Image;
use App\Repository\ImageRepositoryInterface;
use App\Services\Image\ThumbnailGenerator\ThumbnailGeneratorInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;

class ImageService implements ImageServiceInterface
{
    private ImageRepositoryInterface $imageRepository;

    private ThumbnailGeneratorInterface $thumbnailGenerator;

    public function __construct(
        ImageRepositoryInterface    $imageRepository,
        ThumbnailGeneratorInterface $thumbnailGenerator
    ) {
        $this->imageRepository = $imageRepository;
        $this->thumbnailGenerator = $thumbnailGenerator;
    }

    public function saveImagesFromRequest(
        Request $request,
        string  $formName,
        int     $fossilId,
        bool    $isNewFossil = false
    ): array {
        $imageEntities = [];
        $files = $request->files->get($formName);

        $uploadedTmpFiles = null;
        if (is_array($files) && array_key_exists(self::IMAGE_ARRAY_KEY, $files)) {
            $uploadedTmpFiles = $files[self::IMAGE_ARRAY_KEY];
        }

        if (empty($uploadedTmpFiles)) {
            return $imageEntities;
        }

        $isFirst = true;
        /** @var UploadedFile $uploadedTmpFile */
        foreach ($uploadedTmpFiles as $uploadedTmpFile) {
            $image = $this->createImageFromUploadedFile($uploadedTmpFile, $fossilId, $isFirst);

            $uploadedTmpFile->move($image->getAbsolutePath(), $image->getImageName());

            $this->thumbnailGenerator->generate(
                $image->getAbsoluteImagePath(),
                $image->getAbsoluteThumbnailPath(),
                $image->getMimeType()
            );

            $imageEntities[] = $this->imageRepository->saveImage($image);

            $isFirst = false;
        }

        return $imageEntities;
    }

    private function createImageFromUploadedFile(UploadedFile $uploadedFile, int $fossilId, bool $isFirst): Image
    {
        $absoluteBasePath = dirname(__DIR__, 3) . '/public';

        $image = new Image();
        $image->setFossilId($fossilId);
        $image->setIsMainImage($isFirst);
        $image->setShowInGallery($isFirst);
        $image->setMimeType((string) $uploadedFile->getMimeType());
        $image->setRelativePath($this->createHashedRelativePath($uploadedFile));

        $this->createNames($image, $uploadedFile);

        $image->setRelativePath($this->createHashedRelativePath($uploadedFile));
        $image->setAbsolutePath(sprintf(self::PATH_TEMPLATE, $absoluteBasePath, $image->getRelativePath()));

        $image->setRelativeImagePath(sprintf(self::PATH_TEMPLATE, $image->getRelativePath(), $image->getImageName()));
        $image->setRelativeThumbnailPath(sprintf(self::PATH_TEMPLATE, $image->getRelativePath(), $image->getThumbnailName()));

        $image->setAbsoluteImagePath(sprintf(self::PATH_TEMPLATE, $image->getAbsolutePath(), $image->getImageName()));
        $image->setAbsoluteThumbnailPath(sprintf(self::PATH_TEMPLATE, $image->getAbsolutePath(), $image->getThumbnailName()));

        return $image;
    }

    private function createNames(Image $image, UploadedFile $uploadedFile): void
    {
        $timeStamp = (new \DateTime(self::NOW))->getTimestamp();
        $originalFileName = $uploadedFile->getClientOriginalName();
        $random = bin2hex(random_bytes(self::RANDOM_BYTE_LENGTH));

        $image->setImageName(sprintf(self::IMAGE_NAME_TEMPLATE,
            $timeStamp,
            $random,
            $originalFileName
        ));

        $image->setThumbnailName(sprintf(self::THUMBNAIL_NAME_TEMPLATE,
            $timeStamp,
            $random,
            $originalFileName
        ));
    }

    private function createHashedRelativePath(UploadedFile $uploadedFile): string
    {
        $imageHash = hash_file(self::ALGORYTHM, $uploadedFile->getRealPath());
        if (!$imageHash) {
            throw new \RuntimeException('Cannot create Image hash');
        }

        $chunks = array_slice(explode(PHP_EOL, chunk_split($imageHash, self::CHUNK_STRING_LENGTH, PHP_EOL)), self::ARRAY_SLICE_OFFSET, self::ARRAY_SLICE_LENGTH);

        return sprintf(self::PATH_TEMPLATE, self::BASE_PATH, implode('/', $chunks));
    }
}
