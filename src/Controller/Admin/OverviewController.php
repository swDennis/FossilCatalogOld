<?php

namespace App\Controller\Admin;

use App\Repository\FossilRepositoryInterface;
use App\Repository\TagRepositoryInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class OverviewController extends AbstractController
{
    #[Route('/admin', name: 'app_admin')]
    public function fossilList(
        FossilRepositoryInterface $fossilRepository,
        TagRepositoryInterface $tagRepository
    ): Response {
        return $this->render('admin/overview/overview.html.twig', [
            'fossilCount' => $fossilRepository->getFossilListColumnCount([]),
            'categoryCount' => $tagRepository->getTagColumnCount(TagRepositoryInterface::GET_ONLY_CATEGORIES),
            'tagCount' => $tagRepository->getTagColumnCount(TagRepositoryInterface::GET_ONLY_TAGS),
            'categories' => $tagRepository->getList(TagRepositoryInterface::GET_ONLY_CATEGORIES),
        ]);
    }
}