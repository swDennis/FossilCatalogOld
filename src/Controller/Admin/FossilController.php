<?php

namespace App\Controller\Admin;

use App\Entity\Fossil;
use App\Entity\FossilEntity;
use App\Exceptions\IsNotNumericException;
use App\Form\FormEntities\Images;
use App\Form\FossilFormInterface;
use App\Form\ImagesFormInterface;
use App\Repository\FossilFormFieldRepositoryInterface;
use App\Repository\FossilRepositoryInterface;
use App\Repository\ImageRepositoryInterface;
use App\Repository\TagRepositoryInterface;
use App\Services\Filter\RequestFilterFactoryInterface;
use App\Services\Image\ImageServiceInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class FossilController extends AbstractController
{
    #[Route('/admin/fossilList', name: 'app_admin_fossilList')]
    public function fossilList(
        Request                            $request,
        FossilRepositoryInterface          $fossilRepository,
        FossilFormFieldRepositoryInterface $fossilFormFieldRepository,
        RequestFilterFactoryInterface      $requestFilterFactory,
    ): Response {
        $currentPage = $request->get('page', 1);
        if (!is_numeric($currentPage)) {
            throw new IsNotNumericException($this);
        }

        $currentPage = (int) $currentPage;
        if ($currentPage < 1) {
            $currentPage = 1;
        }

        $filter = $requestFilterFactory->addFilterFromRequest($request);
        $filter['page'] = $currentPage;

        $fossilCount = $fossilRepository->getFossilListColumnCount($filter);
        $pagesCount = (int) ceil($fossilCount / FossilRepositoryInterface::FOSSILS_PER_PAGE);

        if ($currentPage > $pagesCount) {
            $currentPage = $pagesCount;
            $filter['page'] = $pagesCount;
        }

        $endPageAdd = 0;
        $startPageAdd = 0;

        $startPage = $currentPage - 5;
        if ($startPage < 1) {
            $endPageAdd = abs($startPage);
            $startPage = 1;
        }

        $endPage = $currentPage + 5 + $endPageAdd;

        if ($endPage > $pagesCount) {
            $startPageAdd = $endPage - $pagesCount;
            $endPage = $pagesCount;
        }

        $startPage -= $startPageAdd;
        if ($startPage < 1) {
            $startPage = 1;
        }

        if ($endPage < $startPage) {
            $endPage = $startPage;
        }

        return $this->render('admin/fossil/list.html.twig', [
            'fossilList' => $fossilRepository->getFossilList($filter),
            'formFields' => $fossilFormFieldRepository->getFossilFormFieldListForOverview(),
            'hasFossils' => (bool) $fossilRepository->getFossilListColumnCount([]),
            'hasActiveFilter' => $this->hasActiveFilter($filter),
            'filters' => $filter,
            'currentPage' => $currentPage,
            'lastPage' => $pagesCount,
            'startPage' => $startPage,
            'endPage' => $endPage,
            'pagesCount' => $pagesCount,
        ]);
    }

    #[Route('/admin/addFossil', name: 'app_admin_add_fossil')]
    public function addFossilForm(
        FossilFormInterface                $fossilForm,
        FossilRepositoryInterface          $fossilRepository,
        FossilFormFieldRepositoryInterface $fossilFormFieldRepository
    ): Response {
        $fossil = new FossilEntity();
        $formBuilder = $this->createFormBuilder($fossil);
        $form = $fossilForm->createForm($formBuilder, $this->generateUrl('app_admin_add_fossil_save'));

        return $this->render('admin/fossil/addFossil.html.twig', $this->createViewAssignForFossilForm($form, $fossilRepository, $fossilFormFieldRepository));
    }

    #[Route('/admin/editFossil', name: 'app_admin_edit_fossil')]
    public function editFossilForm(
        Request                            $request,
        FossilFormInterface                $fossilForm,
        FossilRepositoryInterface          $fossilRepository,
        FossilFormFieldRepositoryInterface $fossilFormFieldRepository
    ): Response {
        $fossilId = $request->get('fossilId');
        if (!is_numeric($fossilId)) {
            throw new IsNotNumericException($this);
        }

        $fossilId = (int) $fossilId;

        $fossil = $fossilRepository->getFossilById($fossilId);

        $formBuilder = $this->createFormBuilder($fossil);
        $form = $fossilForm->createForm($formBuilder, $this->generateUrl('app_admin_add_fossil_save'));

        return $this->render('admin/fossil/addFossil.html.twig',
            $this->createViewAssignForFossilForm($form, $fossilRepository, $fossilFormFieldRepository),
        );
    }

    #[Route('/admin/addFossil/save', name: 'app_admin_add_fossil_save')]
    public function fossilFormSave(
        Request                            $request,
        FossilFormInterface                $fossilForm,
        FossilRepositoryInterface          $fossilRepository,
        FossilFormFieldRepositoryInterface $fossilFormFieldRepository,
        ImageServiceInterface              $imageService,
        TagRepositoryInterface             $tagRepository
    ): Response {
        if (!$request->isMethod('POST')) {
            return $this->redirectToRoute('app_admin_add_fossil');
        }

        $fossil = new FossilEntity();
        $formBuilder = $this->createFormBuilder($fossil);
        $form = $fossilForm->createForm($formBuilder, $this->generateUrl('app_admin_add_fossil_save'));
        $postData = $request->get($form->getName());
        if (!is_array($postData)) {
            throw new \UnexpectedValueException('Expect array');
        }

        $isNewFossil = false;
        if (empty($postData['id'])) {
            $isNewFossil = true;
            $postData['id'] = null;
        } else {
            $postData['id'] = (int) $postData['id'];
        }

        if (empty($postData['findingDate'])) {
            $postData['findingDate'] = null;
        }

        $form->submit($postData);
        $fossil->fromArray($postData);
        $fossil->setTags($tagRepository->getList(TagRepositoryInterface::GET_ONLY_TAGS, $postData['tags']));
        $fossil->setCategories($tagRepository->getList(TagRepositoryInterface::GET_ONLY_CATEGORIES, $postData['categories']));

        $formIsSubmitted = $form->isSubmitted();
        $formIsValid = $form->isValid();

        if (!$formIsSubmitted || !$formIsValid) {
            return $this->render('admin/fossil/addFossil.html.twig',
                $this->createViewAssignForFossilForm($form, $fossilRepository, $fossilFormFieldRepository)
            );
        }

        $savedFossil = $fossilRepository->saveFossil($fossil);

        $imageService->saveImagesFromRequest($request, $form->getName(), (int) $fossil->getId(), $isNewFossil);

        return $this->redirectToRoute('app_admin_fossil_details', ['fossilId' => $savedFossil->getId()]);
    }

    #[Route('/admin/fossil/details', name: 'app_admin_fossil_details')]
    public function fossilDetails(
        Request                            $request,
        FossilRepositoryInterface          $fossilRepository,
        FossilFormFieldRepositoryInterface $fossilFormFieldRepository,
        ImagesFormInterface                $imagesForm
    ): Response {
        $fossilId = $request->get('fossilId');
        if (!is_numeric($fossilId)) {
            return $this->redirectToRoute('app_admin');
        }

        $fossilId = (int) $fossilId;

        $images = new Images();
        $formBuilder = $this->createFormBuilder($images);
        $form = $imagesForm->createForm($formBuilder, $this->generateUrl('app_admin_fossil_details_upload_images'));

        return $this->render('admin/fossil/details.html.twig', [
            'fossil' => $fossilRepository->getFossilById($fossilId),
            'formFields' => $fossilFormFieldRepository->getFossilFormFieldList(),
            'form' => $form,
        ]);
    }

    #[Route('/admin/fossil/details/uploadImages', name: 'app_admin_fossil_details_upload_images')]
    public function uploadImages(Request $request, ImageServiceInterface $imageService, ImagesFormInterface $imagesForm): Response
    {
        $images = new Images();
        $formBuilder = $this->createFormBuilder($images);
        $form = $imagesForm->createForm($formBuilder, $this->generateUrl('app_admin_fossil_details_upload_images'));
        $fossilId = $request->get('fossilId');
        if (!is_numeric($fossilId)) {
            return $this->redirectToRoute('app_admin');
        }

        $fossilId = (int) $fossilId;

        $imageService->saveImagesFromRequest($request, $form->getName(), $fossilId, false);

        return $this->redirectToRoute('app_admin_fossil_details', ['fossilId' => $fossilId]);
    }

    #[Route('/admin/fossil/delete', name: 'app_admin_fossil_delete')]
    public function deleteFossil(
        Request                   $request,
        FossilRepositoryInterface $fossilRepository,
        ImageRepositoryInterface  $imageRepository
    ): Response {
        $fossilId = $request->get('fossilId');
        $givenFossilNumber = $request->get('fossilNumber');

        if (!is_numeric($fossilId) || !is_string($givenFossilNumber)) {
            return new JsonResponse(
                /** @phpstan-ignore-next-line */
                ['message' => sprintf('Cannot delete Fossil with ID: %s, and NUMBER %s', $fossilId, $givenFossilNumber)],
                Response::HTTP_BAD_REQUEST
            );
        }

        $fossilId = (int) $fossilId;

        $imageIds = array_column($imageRepository->getImagesByFossilId($fossilId), 'id');
        $fossil = $fossilRepository->getFossilById($fossilId);
        if (!$fossil instanceof FossilEntity) {
            return new JsonResponse(
                ['message' => sprintf('Cannot find Fossil with ID: %s', $fossilId)],
                Response::HTTP_BAD_REQUEST
            );
        }

        $fossilRepository->deleteFossil((int) $fossil->getId());

        foreach ($imageIds as $imageId) {
            $imageRepository->deleteImage($imageId);
        }

        return new JsonResponse(['message' => 'ok']);
    }

    /**
     * @return array<string, mixed>
     */
    private function createViewAssignForFossilForm(
        FormInterface                      $form,
        FossilRepositoryInterface          $fossilRepository,
        FossilFormFieldRepositoryInterface $fossilFormFieldRepository
    ): array {
        return [
            'form' => $form->createView(),
            'formFields' => $fossilFormFieldRepository->getFossilFormFieldList(),
        ];
    }

    /**
     * @param array<string, mixed> $filter
     */
    private function hasActiveFilter(array $filter): bool
    {
        if (array_key_exists('searchTerm', $filter) && !empty($filter['searchTerm'])) {
            return true;
        }

        if (array_key_exists('categories', $filter) && !empty($filter['categories'])) {
            return true;
        }

        if (array_key_exists('tags', $filter) && !empty($filter['tags'])) {
            return true;
        }

        return false;
    }
}
