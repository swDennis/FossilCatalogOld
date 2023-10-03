<?php

namespace App\Controller\Admin;

use App\Entity\FossilFormField;
use App\Exceptions\IsNotNumericException;
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
            'isFormFieldEdit' => false,
        ];

        return $this->render('admin/fossilForm/addFormFieldForm.html.twig', $viewAssign);
    }

    #[Route('/admin/settings/editForm/addField/save', name: 'app_admin_settings_edit_form_add_field_save')]
    public function addFossilFormFieldSave(
        Request                            $request,
        FossilFormFieldFormInterface       $fossilFormFieldForm,
        FossilFormFieldRepositoryInterface $fossilFormFieldRepository,
        FossilFormEntityDatabaseCreator    $fossilFormEntityDatabaseCreator,
        FossilFormEntityCreator            $fossilFormEntityCreator
    ): Response {
        if (!$request->isMethod('POST')) {
            return $this->redirectToRoute('app_admin_settings_edit_form_add_field');
        }

        $isFormFieldEdit = (bool) $request->get('isFormFieldEdit');

        $fossilFormField = new FossilFormField();
        $formBuilder = $this->createFormBuilder($fossilFormField);
        $form = $fossilFormFieldForm->createForm($formBuilder, $this->generateUrl('app_admin_settings_edit_form_add_field_save'));
        $postData = $this->getPostData($request, $form->getName());

        if ($isFormFieldEdit && !empty($postData['id'] && is_numeric($postData['id']))) {
            $savedFormField = $fossilFormFieldRepository->getFossilFormFieldById((int) $postData['id']);
            if ($savedFormField instanceof FossilFormField) {
                $postData['fieldName'] = $savedFormField->getFieldName();
                $postData['fieldType'] = $savedFormField->getFieldType();
            }
        }

        $form->submit($postData);
        $fossilFormField->fromArray($postData);

        $formIsSubmitted = $form->isSubmitted();
        $formIsValid = $form->isValid();
        if (!$formIsSubmitted || !$formIsValid) {
            return $this->render('admin/fossilForm/addFormFieldForm.html.twig', ['form' => $form->createView(), 'isFormFieldEdit' => false]);
        }

        $fossilFormFieldRepository->saveFossilFormField($fossilFormField);
        $fossilFormEntityDatabaseCreator->addDatabaseColumns();
        $fossilFormEntityCreator->createFossilFormEntity();

        return $this->redirectToRoute('app_admin_settings_edit_form');
    }

    #[Route('/admin/settings/editForm/addField/delete', name: 'app_admin_settings_edit_form_add_field_delete')]
    public function deleteFossilFormField(
        Request                            $request,
        FossilFormFieldRepositoryInterface $fossilFormFieldRepository,
        FossilFormEntityCreator            $fossilFormEntityCreator
    ): Response {


        try {
            $formFieldId = $request->get('formFieldId');
            if (!is_numeric($formFieldId)) {
                throw new IsNotNumericException($this);
            }

            $fossilFormFieldRepository->deleteFossilFormField((int) $formFieldId);
        } catch (\Exception $exception) {
            return new JsonResponse(['message' => $exception->getMessage(), 'trace' => $exception->getTraceAsString()], Response::HTTP_BAD_REQUEST);
        }

        $fossilFormEntityCreator->createFossilFormEntity();

        return new JsonResponse(['message' => 'ok']);
    }

    #[Route('/admin/settings/editForm/addField/edit', name: 'app_admin_settings_edit_form_add_field_edit')]
    public function editFossilFormField(
        Request                            $request,
        FossilFormFieldFormInterface       $fossilFormFieldForm,
        FossilFormFieldRepositoryInterface $fossilFormFieldRepository
    ): Response {
        $formFieldId = $request->get('formFieldId');
        if (!is_numeric($formFieldId)) {
            throw new IsNotNumericException($this);
        }

        $fossilFormField = $fossilFormFieldRepository->getFossilFormFieldById((int) $formFieldId);
        if (!$fossilFormField instanceof FossilFormField) {
            throw new \UnexpectedValueException('Expect FossilFormField, got ' . gettype($fossilFormField));
        }

        $formBuilder = $this->createFormBuilder($fossilFormField);
        $form = $fossilFormFieldForm->createForm($formBuilder, $this->generateUrl('app_admin_settings_edit_form_add_field_save'));
        $form->submit($fossilFormField->toArray());

        return $this->render('admin/fossilForm/addFormFieldForm.html.twig', ['form' => $form->createView(), 'isFormFieldEdit' => true]);
    }

    /**
     * @return array<string, mixed>
     */
    private function getPostData(Request $request, string $formName): array
    {
        $postData = $request->get($formName);
        if (!is_array($postData)) {
            throw new \UnexpectedValueException('Expect array as postData, got ' . gettype($postData));
        }

        $id = $postData[FossilFormFieldRepositoryInterface::FORM_FIELD_COLUMN_FIELD_ID];
        if (is_numeric($id)) {
            $id = (int) $id;
        }

        if ($id <= 0) {
            $id = null;
        }

        $order = $postData[FossilFormFieldRepositoryInterface::FORM_FIELD_COLUMN_FIELD_ORDER];
        if (is_numeric($order)) {
            $order = (int) $order;
        }

        $postData[FossilFormFieldRepositoryInterface::FORM_FIELD_COLUMN_FIELD_ID] = $id;
        $postData[FossilFormFieldRepositoryInterface::FORM_FIELD_COLUMN_FIELD_ORDER] = $order;

        if (!array_key_exists(FossilFormFieldRepositoryInterface::FORM_FIELD_COLUMN_FIELD_SHOW_IN_OVERVIEW, $postData)) {
            $postData[FossilFormFieldRepositoryInterface::FORM_FIELD_COLUMN_FIELD_SHOW_IN_OVERVIEW] = false;
        }

        return $postData;
    }
}