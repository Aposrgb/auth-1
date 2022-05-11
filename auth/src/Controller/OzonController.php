<?php

namespace App\Controller;

use App\Service\OzonService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route("/ozon")]
class OzonController extends AbstractController
{
    public function __construct(
        protected OzonService $ozonService
    )
    {
    }


    #[Route(path: '/category', name: 'ozon_category')]
    public function category(Request $request): Response
    {
        $url = $request->query->all()['url']??null;
        return $this->render('ozon/category'.($url != '' ? 'Sale':'').'.html.twig',
            $this->ozonService->getCategory($url != ''?$url:null)
        );
    }
    #[Route(path: '/seller', name: 'ozon_seller')]
    public function seller(): Response
    {
        return $this->render('ozon/seller.html.twig');
    }
    #[Route(path: '/brand', name: 'ozon_brand')]
    public function brand(): Response
    {
        return $this->render('ozon/brand.html.twig');
    }
    #[Route(path: '/search', name: 'ozon_search')]
    public function search(): Response
    {
        return $this->render('ozon/search.html.twig');
    }
}