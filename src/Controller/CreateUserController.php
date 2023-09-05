<?php

namespace App\Controller;

use App\Entity\InstallationUserData;
use App\Form\CreateUserFormInterface;
use App\Services\Installation\CreateUserServiceInterface;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

class CreateUserController extends AbstractController
{
    private const USER_ERROR_SESSION_KEY = 'user_errors';

    #[Route('/install/collectUserInformation', name: 'app_install_collect_user_information')]
    public function createUser(
        CreateUserFormInterface $createUserForm,
        SessionInterface $session
    ): Response {
        $installationUserData = new InstallationUserData();
        $formBuilder = $this->createFormBuilder($installationUserData);
        $form = $createUserForm->createForm($formBuilder, $this->generateUrl('app_install_save_user'));

        $viewAssign = [
            'form' => $form->createView(),
        ];

        if ($session->has(self::USER_ERROR_SESSION_KEY)) {
            $viewAssign['errors'] = $session->get(self::USER_ERROR_SESSION_KEY);
            $session->remove(self::USER_ERROR_SESSION_KEY);
        }

        return $this->render('install/createUser.html.twig', $viewAssign);
    }

    #[Route('/install/saveUser', name: 'app_install_save_user')]
    public function saveUser(
        Request $request,
        SessionInterface $session,
        CreateUserFormInterface $createUserForm,
        CreateUserServiceInterface $createUserService
    ): Response {
        if (!$request->isMethod('POST')) {
            return $this->redirectToRoute('app_install_collect_user_information');
        }

        $installationUserData = new InstallationUserData();
        $formBuilder = $this->createFormBuilder($installationUserData);
        $form = $createUserForm->createForm($formBuilder, $this->generateUrl('app_install_save_user'));
        $postData = $request->get($form->getName());
        $form->submit($postData);
        $installationUserData->fromArray($postData);

        if ($installationUserData->getPassword() !== $installationUserData->getPasswordConfirm()) {
            $session->set(self::USER_ERROR_SESSION_KEY, ['errorMessage' => 'The passwords do not match']);

            return $this->redirectToRoute('app_install_collect_user_information');
        }

        $user = $createUserService->createUser($installationUserData->getEmail(), $installationUserData->getPassword());

        try {
            $createUserService->saveUser($user);
        } catch (Exception $exception) {
            $errorContext = [
                'errorMessage' => $exception->getMessage(),
                'errorTrace' => $exception->getTraceAsString(),
            ];

            $previousException = $exception->getPrevious();
            if ($previousException instanceof Exception) {
                $errorContext['previousErrorMessage'] = $previousException->getMessage();
                $errorContext['previousErrorTrace'] = $previousException->getTraceAsString();
            }

            $session->set(self::USER_ERROR_SESSION_KEY, $errorContext);

            return $this->redirectToRoute('app_install_collect_user_information');
        }

        return $this->redirectToRoute('app_login');
    }
}
