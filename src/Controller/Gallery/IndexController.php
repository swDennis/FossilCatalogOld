<?php

namespace App\Controller\Gallery;

use App\Services\Installation\InstallationServiceInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class IndexController extends AbstractController
{
    #[Route('/', name: 'app_index')]
    public function index(InstallationServiceInterface $installationService): Response
    {
        if (!$installationService->checkLockFileExists()) {
            return $this->redirectToRoute('app_install_collect_information');
        }

        return $this->render('index.html.twig', [
            'controller_name' => 'IndexController',
        ]);
    }
}
