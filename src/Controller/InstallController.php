<?php

namespace App\Controller;

use App\Entity\InstallationData;
use App\Exceptions\DatabaseCreationException;
use App\Exceptions\DatabaseExistsException;
use App\Form\InstallationFormInterface;
use App\Services\FossilForm\FossilFormEntityCreator;
use App\Services\Installation\InstallationConnectionInterface;
use App\Services\Installation\InstallationServiceInterface;
use App\Setup\CreateDatabaseInterface;
use App\Setup\CreateDatabaseTablesInterface;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

class InstallController extends AbstractController
{
    private const INSTALLATION_ERROR_SESSION_KEY = 'installation_errors';

    #[Route('/install/collectInformation', name: 'app_install_collect_information')]
    public function index(InstallationFormInterface $installationForm, SessionInterface $session): Response
    {
        $installationData = new InstallationData();
        $formBuilder = $this->createFormBuilder($installationData);
        $form = $installationForm->createForm($formBuilder, $this->generateUrl('app_install_execute'));

        $viewAssign = [
            'form' => $form->createView(),
        ];

        if ($session->has(self::INSTALLATION_ERROR_SESSION_KEY)) {
            $viewAssign['errors'] = $session->get(self::INSTALLATION_ERROR_SESSION_KEY);
            $session->remove(self::INSTALLATION_ERROR_SESSION_KEY);
        }

        return $this->render('install/index.html.twig', $viewAssign);
    }

    #[Route('/install/execute', name: 'app_install_execute')]
    public function execute(
        Request $request,
        InstallationFormInterface $installationForm,
        InstallationServiceInterface $installationService,
        InstallationConnectionInterface $installationConnection,
        CreateDatabaseInterface $createDatabase,
        CreateDatabaseTablesInterface $createDatabaseTables,
        SessionInterface $session
    ): Response {
        if ($installationService->checkLockFileExists()) {
            return $this->redirectToRoute('app_logout');
        }

        if (!$request->isMethod('POST')) {
            return $this->redirectToRoute('app_install_collect_information');
        }

        $installationData = new InstallationData();
        $formBuilder = $this->createFormBuilder($installationData);
        $form = $installationForm->createForm($formBuilder, $this->generateUrl('app_install_execute'));
        $postData = $request->get($form->getName());
        $form->submit($postData);
        $installationData->fromArray($postData);
        $installationData->setAppSecret($installationService->createAppSecret());

        $formIsSubmitted = $form->isSubmitted();
        $formIsValid = $form->isValid();
        if (!$formIsSubmitted || !$formIsValid) {
            return $this->render('install/index.html.twig', [
                $form->getName() => $form->createView(),
            ]);
        }

        try {
            $connection = $installationConnection->createPDOConnection($installationData);
        } catch (Exception $exception) {
            return $this->redirectToCollectInformationWithErrors($exception, $session);
        }

        try {
            $createDatabase->createDatabase($installationData, $connection);
        } catch (DatabaseExistsException $databaseExistsException) {
            // do nothing
        } catch (DatabaseCreationException $databaseCreationException) {
            return $this->redirectToCollectInformationWithErrors($databaseCreationException, $session);
        } catch (Exception $exception) {
            return $this->redirectToCollectInformationWithErrors($exception, $session);
        }

        try {
            $createDatabaseTables->createDatabaseTables($installationData, $connection);
        } catch (Exception $exception) {
            return $this->redirectToCollectInformationWithErrors($exception, $session);
        }

        try {
            $installationService->createDonEnvFile($installationData);
        } catch (Exception $exception) {
            return $this->redirectToCollectInformationWithErrors($exception, $session);
        }

        try {
            $installationService->createInstallLockFile();
        } catch (Exception $exception) {
            return $this->redirectToCollectInformationWithErrors($exception, $session);
        }

        return $this->redirectToRoute('app_install_execute_create_entities');
    }

    #[Route('/install/execute/create/entities', name: 'app_install_execute_create_entities')]
    public function createEntities(FossilFormEntityCreator $fossilFormEntityCreator)
    {
        $fossilFormEntityCreator->createFossilFormEntity();

        return $this->redirectToRoute('app_install_collect_user_information');
    }

    private function redirectToCollectInformationWithErrors(Exception $exception, SessionInterface $session): Response
    {
        $errorContext = [
            'errorMessage' => $exception->getMessage(),
            'errorTrace' => $exception->getTraceAsString(),
        ];

        $previousException = $exception->getPrevious();
        if ($previousException instanceof Exception) {
            $errorContext['previousErrorMessage'] = $previousException->getMessage();
            $errorContext['previousErrorTrace'] = $previousException->getTraceAsString();
        }

        $session->set(self::INSTALLATION_ERROR_SESSION_KEY, $errorContext);

        return $this->redirectToRoute('app_install_collect_information');
    }
}
