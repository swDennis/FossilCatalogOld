<?php

namespace App\Controller\Admin;

use App\Entity\FossilFormField;
use App\Form\FormBuilder\FossilFormFieldFormInterface;
use App\Repository\FossilFormFieldRepositoryInterface;
use App\Services\FossilForm\FossilFormEntityCreator;
use App\Services\FossilForm\FossilFormEntityDatabaseCreator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class FossilsFormController extends AbstractController
{
    #[Route('/admin/settings/editForm', name: 'app_admin_settings_edit_form')]
    public function formFossilFieldList(FossilFormFieldRepositoryInterface $fossilFormFieldRepository): Response
    {
        $viewAssign = [
            'formFieldList' => $fossilFormFieldRepository->getFossilFormFieldList(),
        ];

        return $this->render('admin/fossilForm/editForm.html.twig', $viewAssign);
    }

    #[Route('/admin/settings/editForm/addField', name: 'app_admin_settings_edit_form_add_field')]
    public function addFossilFormField(FossilFormFieldFormInterface $fossilFormFieldForm, FossilFormFieldRepositoryInterface $fossilFormFieldRepository): Response
    {
        $fossilFormField = new FossilFormField();
        $fossilFormField->setFieldOrder($fossilFormFieldRepository->getNewOrderNumber());
        $formBuilder = $this->createFormBuilder($fossilFormField);
        $form = $fossilFormFieldForm->createForm($formBuilder, $this->generateUrl('app_admin_settings_edit_form_add_field_save'));

        $viewAssign = [
            'form' => $form->createView(),
        ];

        return $this->render('admin/fossilForm/addFormFieldForm.html.twig', $viewAssign);
    }

    #[Route('/admin/settings/editForm/addField/save', name: 'app_admin_settings_edit_form_add_field_save')]
    public function addFossilFormFieldSave(
        Request $request,
        FossilFormFieldFormInterface $fossilFormFieldForm,
        FossilFormFieldRepositoryInterface $fossilFormFieldRepository,
        FossilFormEntityDatabaseCreator $fossilFormEntityDatabaseCreator,
        FossilFormEntityCreator $fossilFormEntityCreator
    ): Response {
        if (!$request->isMethod('POST')) {
            return $this->redirectToRoute('app_admin_settings_edit_form_add_field');
        }

        $fossilFormField = new FossilFormField();
        $formBuilder = $this->createFormBuilder($fossilFormField);
        $form = $fossilFormFieldForm->createForm($formBuilder, $this->generateUrl('app_admin_settings_edit_form_add_field_save'));
        $postData = $this->getPostData($request, $form->getName());

        $form->submit($postData);
        $fossilFormField->fromArray($postData);

        $formIsSubmitted = $form->isSubmitted();
        $formIsValid = $form->isValid();
        if (!$formIsSubmitted || !$formIsValid) {
            return $this->render('admin/fossilForm/addFormFieldForm.html.twig', ['form' => $form->createView()]);
        }

        $fossilFormFieldRepository->saveFossilFormField($fossilFormField);
        $fossilFormEntityDatabaseCreator->addDatabaseColumns();
        $fossilFormEntityCreator->createFossilFormEntity();

        return $this->redirectToRoute('app_admin_settings_edit_form');
    }

    #[Route('/admin/settings/editForm/addField/delete', name: 'app_admin_settings_edit_form_add_field_delete')]
    public function deleteFossilFormField(
        Request $request,
        FossilFormFieldRepositoryInterface $fossilFormFieldRepository,
        FossilFormEntityCreator $fossilFormEntityCreator
    ) {
        $formFieldId = (int) $request->get('formFieldId');

        try {
            $fossilFormFieldRepository->deleteFossilFormField($formFieldId);
        } catch (\Exception $exception) {
            return new JsonResponse(['message' => $exception->getMessage(), 'trace' => $exception->getTraceAsString()], Response::HTTP_BAD_REQUEST);
        }

        $fossilFormEntityCreator->createFossilFormEntity();

        return new JsonResponse(['message' => 'ok']);
    }

    #[Route('/admin/settings/editForm/addField/edit', name: 'app_admin_settings_edit_form_add_field_edit')]
    public function editFossilFormField(
        Request $request,
        FossilFormFieldFormInterface $fossilFormFieldForm,
        FossilFormFieldRepositoryInterface $fossilFormFieldRepository
    ) {
        $formFieldId = (int) $request->get('formFieldId');

        $fossilFormFieldArray = $fossilFormFieldRepository->getFossilFormFieldById($formFieldId);

        $fossilFormField = new FossilFormField();
        $fossilFormField->fromArray($fossilFormFieldArray);

        $formBuilder = $this->createFormBuilder($fossilFormField);
        $form = $fossilFormFieldForm->createForm($formBuilder, $this->generateUrl('app_admin_settings_edit_form_add_field_save'));
        $form->submit($fossilFormFieldArray);

        return $this->render('admin/fossilForm/addFormFieldForm.html.twig', ['form' => $form->createView()]);
    }

    private function getPostData(Request $request, string $formName): array
    {
        $postData = $request->get($formName);

        $id = (int) $postData[FossilFormFieldRepositoryInterface::FORM_FIELD_COLUMN_FIELD_ID];
        if ($id <= 0) {
            $id = null;
        }

        $postData[FossilFormFieldRepositoryInterface::FORM_FIELD_COLUMN_FIELD_ID] = $id;
        $postData[FossilFormFieldRepositoryInterface::FORM_FIELD_COLUMN_FIELD_ORDER] = (int) $postData[FossilFormFieldRepositoryInterface::FORM_FIELD_COLUMN_FIELD_ORDER];

        if (!array_key_exists(FossilFormFieldRepositoryInterface::FORM_FIELD_COLUMN_FIELD_SHOW_IN_OVERVIEW, $postData)) {
            $postData[FossilFormFieldRepositoryInterface::FORM_FIELD_COLUMN_FIELD_SHOW_IN_OVERVIEW] = false;
        }

        return $postData;
    }
}