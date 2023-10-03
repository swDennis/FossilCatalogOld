<?php

namespace App\Controller\Admin;

use App\Entity\Tag;
use App\Form\TagFormInterface;
use App\Repository\TagRepositoryInterface;
use App\Validator\TagName\TagInfo;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;

class TagController extends AbstractController
{
    #[Route('/admin/tag/list', name: 'app_admin_tag_list')]
    public function list(
        TagRepositoryInterface $tagRepository
    ): Response {
        return $this->render('admin/tag/list.html.twig', [
            'tagList' => $tagRepository->getList(TagRepositoryInterface::GET_ALL, []),
        ]);
    }

    #[Route('/admin/tag/create/or/edit', name: 'app_admin_tag_create_or_edit')]
    public function createEditTag(
        Request $request,
        TagRepositoryInterface $tagRepository,
        TagFormInterface $tagForm
    ): Response {
        $id = $request->get('id');
        if (!is_numeric($id)) {
            $id = null;
        }

        $tagId = (int) $id;

        $tag = new Tag();
        $formBuilder = $this->createFormBuilder($tag);
        $postData = $this->getPostData($request, 'form');

        $tagInfo = new TagInfo($tagRepository, $tagId, $postData);
        $form = $tagForm->createForm($formBuilder, $this->generateUrl('app_admin_tag_create_or_edit'), $tagInfo);

        if ($tagId) {
            $existingTag = $tagRepository->getById($tagId);
            if ($existingTag instanceof Tag) {
//                $form->submit($existingTag);
                $tag = $existingTag;
            }
        }

        if (!$request->isMethod('POST')) {
            return $this->render('admin/tag/form.html.twig', ['form' => $form->createView()]);
        }

        $form->submit($postData);

        $tag->fromArray($postData);
        $formIsSubmitted = $form->isSubmitted();

        $formIsValid = $form->isValid();
        if (!$formIsSubmitted || !$formIsValid) {
            return $this->render('admin/tag/form.html.twig', ['form' => $form->createView()]);
        }

        $tagRepository->saveTag($tag);

        return $this->redirectToRoute('app_admin_tag_list');
    }

    #[Route('/admin/tag/delete', name: 'app_admin_tag_delete')]
    public function tagDelete(
        Request $request,
        TagRepositoryInterface $tagRepository
    ): JsonResponse {
        $id = $request->get('id');
        if (!is_numeric($id)) {
            $id = null;
        }

        $tagId = (int) $id;
        if (!$tagId) {
            return new JsonResponse(['message' => 'No "tagId" provided'], Response::HTTP_BAD_REQUEST);
        }

        $tagArray = $tagRepository->getById($tagId);
        if (empty($tagArray)) {
            return new JsonResponse(['message' => sprintf('Cannot find Tag with ID: %s', $tagId)], Response::HTTP_BAD_REQUEST);
        }

        $tagRepository->deleteTag($tagId);

        return new JsonResponse(['message' => 'ok']);
    }

    #[Route('/admin/tag/load/available', name: 'app_admin_tag_load_available')]
    public function loadAvailableTags(
        Request $request,
        TagRepositoryInterface $tagRepository
    ): JsonResponse {
        $selectField = $request->get('selectField');
        $values = $request->get('values');

        return new JsonResponse([
            /** @phpstan-ignore-next-line */
            'tags' => $tagRepository->getTagsThatAreAssignedToFossils($selectField, explode(',', $values)),
        ]);
    }

    /**
     * @return array<string, string|int>
     */
    private function getPostData(Request $request, string $formName): array
    {
        $postData = $request->get($formName);
        if (!is_array($postData)) {
            return [];
        }

        if (array_key_exists('isUsedAsCategory', $postData)) {
            $postData['isUsedAsCategory'] = (int) $postData['isUsedAsCategory'];
        }

        if (array_key_exists('id', $postData)) {
            $id = (int) $postData['id'];
            if ($id <= 0) {
                $postData['id'] = null;
            }
        }

        return $postData;
    }
}