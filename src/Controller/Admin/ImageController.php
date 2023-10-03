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

        if (!is_numeric($imageId)) {
            return new JsonResponse(['message' => 'Request expects valid an ImageId, got ' . gettype($imageId)], Response::HTTP_BAD_REQUEST);
        }

        $image = $imageRepository->getImageById((int) $imageId);
        if (!$image instanceof Image) {
            return new JsonResponse(['message' => 'The provided ImageId cannot be found'], Response::HTTP_BAD_REQUEST);
        }

        try {
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

        if (!is_numeric($fossilId)) {
            return new JsonResponse(['message' => 'Request expects an fossilId, got' . gettype($fossilId)], Response::HTTP_BAD_REQUEST);
        }

        if (!is_numeric($imageId)) {
            return new JsonResponse(['message' => 'Request expects an ImageId, got' . gettype($imageId)], Response::HTTP_BAD_REQUEST);
        }

        $newMainImage = $imageRepository->getImageById((int) $imageId);
        if (!$newMainImage instanceof Image) {
            return new JsonResponse(['message' => 'The provided NewImageId cannot be found'], Response::HTTP_BAD_REQUEST);
        }

        $oldMainImage = $imageRepository->getMainImageByFossilId((int) $fossilId);
        if (!$oldMainImage instanceof Image) {
            return new JsonResponse(['message' => 'The provided OldImageId cannot be found'], Response::HTTP_BAD_REQUEST);
        }

        try {
            $oldMainImage->setIsMainImage(false);
            $imageRepository->saveImage($oldMainImage);

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
        if (!is_numeric($imageId)) {
            return new JsonResponse(['message' => 'No valid imageId provided'], Response::HTTP_BAD_REQUEST);
        }

        $image = $imageRepository->getImageById((int) $imageId);
        if (!$image instanceof Image) {
            return new JsonResponse(['message' => 'The provided OldImageId cannot be found'], Response::HTTP_BAD_REQUEST);
        }

        if ($image->getIsMainImage()) {
            return new JsonResponse(['message' => 'This is the main image and can not be deleted. First set another image as main image.'], Response::HTTP_BAD_REQUEST);
        }

        try {
            if (is_file($image->getAbsoluteImagePath())) {
                unlink($image->getAbsoluteImagePath());
            }

            if (is_file($image->getAbsoluteThumbnailPath())) {
                unlink($image->getAbsoluteThumbnailPath());
            }

            $imageRepository->deleteImage((int) $image->getId());
        } catch (\Exception $exception) {
            return new JsonResponse(['message' => $exception->getMessage(), 'trace' => $exception->getTraceAsString()], Response::HTTP_BAD_REQUEST);
        }

        return new JsonResponse(['message' => 'ok']);
    }
}