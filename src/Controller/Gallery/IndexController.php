<?php

namespace App\Controller\Gallery;

use App\Repository\FossilRepositoryInterface;
use App\Repository\ImageRepositoryInterface;
use App\Services\Installation\InstallationServiceInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class IndexController extends AbstractController
{
    #[Route('/', name: 'gallery_index')]
    public function index(
        InstallationServiceInterface $installationService,
        ImageRepositoryInterface     $imageRepository,
        FossilRepositoryInterface    $fossilRepository
    ): Response {
        if (!$installationService->checkLockFileExists()) {
            return $this->redirectToRoute('app_install_collect_information');
        }

        $titleImage = $imageRepository->getRandomTitleImage();

        if (empty($titleImage)) {
            $titleImage = null;
        }

//// TODO: REMOVE AFTER DEBUG
//echo'<pre>';
//var_export($fossilRepository->getFossilList(['page' => 1]));
//echo '<br/>';
//die();
//// TODO: REMOVE AFTER DEBUG

        return $this->render('index.html.twig', [
            'controller_name' => 'IndexController',
            'titleImage' => $titleImage,
            // TODO: Add Filter
            'fossils' => $fossilRepository->getFossilList(['page' => 1])
        ]);
    }
}
