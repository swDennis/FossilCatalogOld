<?php

namespace App\Controller\Admin;

use App\Entity\ImportEntity;
use App\Form\ImportFormInterface;
use App\Import\ImportFileValidatorInterface;
use App\Import\ImportServiceInterface;
use App\Import\ImportUploadServiceInterface;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ImportController extends AbstractController
{
    #[Route('/admin/import', name: 'app_admin_import')]
    public function importIndex(
        ImportFormInterface $importForm
    ): Response {
        $import = new ImportEntity();
        $formBuilder = $this->createFormBuilder($import);
        $form = $importForm->createForm($formBuilder, $this->generateUrl('app_admin_import_upload_file'));

        return $this->render('admin/Import/index.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/admin/import/upload/file', name: 'app_admin_import_upload_file')]
    public function uploadImportFile(
        Request $request,
        ImportUploadServiceInterface $importUploadService,
        ImportFileValidatorInterface $importFileValidator
    ): Response {
        try {
            $file = $importUploadService->moveToImportDirectory($request, 'form', 'import');
            $importFileValidator->validate($file);
        } catch (\Exception $exception) {
            $this->addFlash('Exception', $exception->getMessage() . '<br><br>' . $exception->getTraceAsString());

            return $this->redirectToRoute('app_admin_import');
        }

        return $this->redirectToRoute('app_admin_import_overview', ['importFile' => $file->getPathname()]);
    }

    #[Route('/admin/import/overview', name: 'app_admin_import_overview')]
    public function importOverview(
        Request $request,
        ImportFileValidatorInterface $importFileValidator,
        ImportServiceInterface $importService
    ): Response {
        $file = $request->get('importFile');
        if (!is_string($file) || !is_file($file)) {
            $this->addFlash('Failure', 'Cannot read import file: ' . $file);

            return $this->redirectToRoute('app_admin_import');
        }

        $file = new File($file);

        try {
            $directory = $importFileValidator->validateContentStructure($file);
        } catch (\Exception $exception) {
            $this->addFlash('Exception', $exception->getMessage() . '<br><br>' . $exception->getTraceAsString());

            return $this->redirectToRoute('app_admin_import');
        }

        $data = $importService->analyzeData($directory);

        return $this->render('admin/Import/progress.html.twig', [
            'data' => $data,
        ]);
    }

    #[Route('/admin/import/execution', name: 'app_admin_import_execution')]
    public function importExecution(
        ImportServiceInterface $importService
    ): JsonResponse {
        try {
            $status = $importService->import();
            session_write_close();
        } catch (Exception $exception) {
            session_write_close();
            return new JsonResponse(['message' => $exception->getMessage()], Response::HTTP_BAD_REQUEST);
        }

        return new JsonResponse(['message' => 'ok', 'status' => $status], Response::HTTP_OK);
    }

    #[Route('/admin/import/clear', name: 'app_admin_import_clear')]
    public function importClearSession(
        ImportServiceInterface $importService
    ): JsonResponse {
        try {
            $importService->clearSession();
        } catch (Exception $exception) {
            return new JsonResponse(['message' => $exception->getMessage()], Response::HTTP_BAD_REQUEST);
        }

        return new JsonResponse(['message' => 'ok'], Response::HTTP_OK);
    }
}