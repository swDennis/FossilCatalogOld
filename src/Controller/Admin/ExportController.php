<?php

namespace App\Controller\Admin;

use App\Export\ExportServiceInterface;
use App\Export\Status\Exceptions\MissingArrayKeyException;
use App\Repository\ImportExportRepositoryInterface;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\Routing\Annotation\Route;

class ExportController extends AbstractController
{
    #[Route('/admin/export/overview', name: 'app_admin_export_overview')]
    public function exportOverview(
        ExportServiceInterface          $exportService,
        ImportExportRepositoryInterface $importExportRepository
    ): Response {
        return $this->render('admin/Export/overview.html.twig', [
            'data' => $exportService->analyzeData(),
            'exports' => $importExportRepository->getExports(),
        ]);
    }

    #[Route('/admin/export', name: 'app_admin_export')]
    public function export(
        ExportServiceInterface $exportService
    ): JsonResponse {
        try {
            $exportService->initializeFiles();
            $status = $exportService->export();

            session_write_close();
        } catch (Exception $exception) {
            session_write_close();
            return new JsonResponse(['message' => $exception->getMessage()], Response::HTTP_BAD_REQUEST);
        }

        return new JsonResponse(['message' => 'ok', 'status' => $status], Response::HTTP_OK);
    }

    #[Route('/admin/export/clear', name: 'app_admin_export_clear')]
    public function clear(
        ExportServiceInterface $exportService
    ): JsonResponse {
        $exportService->clearSession();

        return new JsonResponse(['message' => 'ok'], Response::HTTP_OK);
    }

    #[Route('/admin/export/zip', name: 'app_admin_export_zip')]
    public function exportZip(
        Request                $request,
        ExportServiceInterface $exportService
    ): JsonResponse {
        $directory = $request->get('directory');
        $fileName = $request->get('name');

        if (!is_string($directory)) {
            return new JsonResponse(['message' => 'No export "directory" provided'], Response::HTTP_BAD_REQUEST);
        }

        if (!is_string($fileName)) {
            return new JsonResponse(['message' => 'No export "file name" provided'], Response::HTTP_BAD_REQUEST);
        }

        try {
            $zipFile = $exportService->createZipFile($directory, $fileName);
        } catch (\Exception $exception) {
            return new JsonResponse(['message' => $exception->getMessage()], Response::HTTP_BAD_REQUEST);
        }

        if (!is_file($zipFile)) {
            return new JsonResponse(['message' => 'There is a problem with the ZipFile'], Response::HTTP_BAD_REQUEST);
        }

        return new JsonResponse(['message' => 'ok', 'zipFile' => $zipFile]);
    }

    #[Route('/admin/download/zip', name: 'app_admin_download_zip')]
    public function download(Request $request): BinaryFileResponse
    {
        $file = $request->get('filename');
        if (!is_string($file)) {
            return new BinaryFileResponse('', Response::HTTP_NO_CONTENT);
        }

        $response = new BinaryFileResponse($file, Response::HTTP_OK, [], false);
        $response->setContentDisposition(
            ResponseHeaderBag::DISPOSITION_ATTACHMENT,
            basename($file)
        );

        return $response;
    }

    #[Route('/admin/export/zip/delete', name: 'app_admin_delete_export_zip')]
    public function deleteExportZip(
        Request                $request,
        ExportServiceInterface $exportService
    ): JsonResponse {
        $directory = $request->get('directory');
        if (!is_string($directory)) {
            return new JsonResponse(['message' => 'Directory string not given'], Response::HTTP_BAD_REQUEST);
        }

        $exportService->deleteBackup($directory);

        return new JsonResponse(['message' => 'ok']);
    }
}