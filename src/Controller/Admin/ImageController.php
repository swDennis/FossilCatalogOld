<?php

namespace App\Controller\Admin;

use App\Entity\Image;
use App\Repository\ImageRepositoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class ImageController extends AbstractController
{
    #[Route('/admin/image/showInGallery', name: 'app_admin_image_show_in_gallery')]
    public function addImageToGallery(Request $request, ImageRepositoryInterface $imageRepository): Response
    {
        $imageId = $request->get('imageId');
        $showInGallery = (bool) $request->get('showInGallery', false);

        if ($imageId === null) {
            return new JsonResponse(['message' => 'Request expects an ImageId'], Response::HTTP_BAD_REQUEST);
        }

        $imageArray = $imageRepository->getImageById($imageId);
        if (empty($imageArray)) {
            return new JsonResponse(['message' => 'The provided ImageId cannot be found'], Response::HTTP_BAD_REQUEST);
        }

        try {
            $image = new Image();
            $image->fromArray($imageArray);
            $image->setShowInGallery($showInGallery);
            $imageRepository->saveImage($image);
        } catch (\Exception $exception) {
            return new JsonResponse(['message' => $exception->getMessage(), 'trace' => $exception->getTraceAsString()], Response::HTTP_BAD_REQUEST);
        }

        return new JsonResponse(['message' => 'ok']);
    }

    #[Route('/admin/image/setAsMainImage', name: 'app_admin_image_set_as_title_image')]
    public function setImageAsMainImage(Request $request, ImageRepositoryInterface $imageRepository): Response
    {
        $fossilId = $request->get('fossilId');
        $imageId = $request->get('imageId');

        if ($fossilId === null) {
            return new JsonResponse(['message' => 'Request expects an fossilId'], Response::HTTP_BAD_REQUEST);
        }

        if ($imageId === null) {
            return new JsonResponse(['message' => 'Request expects an ImageId'], Response::HTTP_BAD_REQUEST);
        }

        $imageArray = $imageRepository->getImageById($imageId);
        if (empty($imageArray)) {
            return new JsonResponse(['message' => 'The provided ImageId cannot be found'], Response::HTTP_BAD_REQUEST);
        }

        $oldMainImagesArray = $imageRepository->getMainImageByFossilId($fossilId);

        try {
            foreach ($oldMainImagesArray as $oldMainImageArray) {
                $oldMainImage = new Image();
                $oldMainImage->fromArray($oldMainImageArray);
                $oldMainImage->setIsMainImage(false);

                $imageRepository->saveImage($oldMainImage);
            }

            $newMainImage = new Image();
            $newMainImage->fromArray($imageArray);
            $newMainImage->setIsMainImage(true);

            $imageRepository->saveImage($newMainImage);

        } catch (\Exception $exception) {
            return new JsonResponse(['message' => $exception->getMessage(), 'trace' => $exception->getTraceAsString()], Response::HTTP_BAD_REQUEST);
        }

        return new JsonResponse(['message' => 'ok']);
    }

    #[Route('/admin/image/delete', name: 'app_admin_image_delete_image')]
    public function deleteImage(Request $request, ImageRepositoryInterface $imageRepository): Response
    {

        $imageId = $request->get('imageId');

        $imageArray = $imageRepository->getImageById($imageId);
        if (empty($imageArray)) {
            return new JsonResponse(['message' => 'The provided imageId: %s cannot be found'], Response::HTTP_BAD_REQUEST);
        }

        $image = new Image();
        $image->fromArray($imageArray);
        if($image->getIsMainImage()) {
            return new JsonResponse(['message' => 'This is the main image and can not be deleted. First set another image as main image.'], Response::HTTP_BAD_REQUEST);
        }

        try {
            if (is_file($image->getAbsoluteImagePath())) {
                unlink($image->getAbsoluteImagePath());
            }

            if (is_file($image->getAbsoluteThumbnailPath())) {
                unlink($image->getAbsoluteThumbnailPath());
            }

            $imageRepository->deleteImage($image->getId());
        } catch (\Exception $exception) {
            return new JsonResponse(['message' => $exception->getMessage(), 'trace' => $exception->getTraceAsString()], Response::HTTP_BAD_REQUEST);
        }

        return new JsonResponse(['message' => 'ok']);
    }
}