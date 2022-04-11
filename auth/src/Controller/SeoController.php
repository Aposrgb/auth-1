<?php

namespace App\Controller;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route("/seo")]
class SeoController extends AbstractController
{
    #[Route(path: '/keyword', name: 'keyword')]
    public function keyword(): Response
    {
        return $this->render('seo/keyword.html.twig');
    }
    #[Route(path: '/keyword/{name}', name: 'keyword_name')]
    public function keywordName($name): Response
    {
        return $this->render('seo/keywordName.html.twig');
    }
    #[Route(path: '/keywords/expanding', name: 'keywords_exp')]
    public function keywordsEx(): Response
    {
        return $this->render('seo/expanding.html.twig');
    }
    #[Route(path: '/keywords/selection', name: 'selection')]
    public function selection(): Response
    {
        return $this->render('seo/selection.html.twig');
    }
    #[Route(path: '/position-tracking', name: 'position_tracking')]
    public function positionTracking(): Response
    {
        return $this->render('seo/position-tracking.twig');
    }
    #[Route(path: '/tools/wb-card-checker', name: 'wb_card_checker')]
    public function toolsCardChecker(): Response
    {
        return $this->render('seo/wb-card-checker.html.twig');
    }
    #[Route(path: '/tools/wb-sku-compare', name: 'wb_sku_compare')]
    public function toolsSkuCompare(): Response
    {
        return $this->render('seo/wb-sku-compare.html.twig');
    }

}