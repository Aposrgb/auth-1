<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route("/ozon")]
class OzonController extends AbstractController
{
    #[Route(path: '/category', name: 'ozon_category')]
    public function keyword(): Response
    {
        return $this->render('ozon/category.html.twig');
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