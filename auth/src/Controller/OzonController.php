<?php

namespace App\Controller;

use App\Service\OzonService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

#[Route("/ozon")]
class OzonController extends AbstractController
{
    public function __construct(
        protected UserPasswordHasherInterface $hasher,
        protected EntityManagerInterface $entityManager,
        protected OzonService $ozonService
    )
    {
        parent::__construct($this->hasher, $this->entityManager, $this->mpStatsApi, $this->mpStatsApiOz, $this->mpStatsApiWb);
    }


    #[Route(path: '/category', name: 'ozon_category')]
    public function category(Request $request): Response
    {
        $check = $this->checkStatusUser();
        if($check) return $check;
        $url = $request->query->all()['url']??null;
        return $this->render('ozon/category'.($url != '' ? 'Sale':'').'.html.twig',
            $this->ozonService->getCategory($url != ''?$url:null , $request->query->all())
        );
    }
    #[Route(path: '/seller', name: 'ozon_seller')]
    public function seller(): Response
    {
        $check = $this->checkStatusUser();
        if($check) return $check;
        return $this->render('ozon/searchSeller.html.twig');
    }
    #[Route(path: '/seller/{seller}', name: 'ozon_find_seller')]
    public function findSeller($seller, Request $request): Response
    {
        $check = $this->checkStatusUser();
        if($check) return $check;
        return $this->render('ozon/seller.html.twig',
            $this->ozonService->findSeller($seller, $request->query->all())
        );
    }
    #[Route(path: '/brand/{brand}', name: 'ozon_find_brand')]
    public function findBrand($brand, Request $request): Response
    {
        $check = $this->checkStatusUser();
        if($check) return $check;
        return $this->render('ozon/brand.html.twig',
            $this->ozonService->findBrand($brand, $request->query->all())
        );
    }
    #[Route(path: '/brand', name: 'ozon_brand')]
    public function brand(): Response
    {
        $check = $this->checkStatusUser();
        if($check) return $check;
        return $this->render('ozon/searchBrand.html.twig');
    }
    #[Route(path: '/search', name: 'ozon_search')]
    public function search(): Response
    {
        $check = $this->checkStatusUser();
        if($check) return $check;
        return $this->render('ozon/search.html.twig');
    }
    #[Route(path: '/search/{sku}', name: 'ozon_get_item')]
    public function getItem($sku, Request $request): Response
    {
        $check = $this->checkStatusUser();
        if($check) return $check;
        return $this->render('ozon/item.html.twig',
            $this->ozonService->getItem($sku, $request->query->all())
        );
    }
}