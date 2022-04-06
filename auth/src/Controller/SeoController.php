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

}