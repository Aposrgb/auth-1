<?php

namespace App\Controller;

use App\Service\WbService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

#[Route("/wb")]
class WbController extends AbstractController
{
    public function __construct(
        protected UserPasswordHasherInterface $hasher,
        protected EntityManagerInterface $entityManager,
        protected WbService $service
    )
    {
        parent::__construct($this->hasher, $this->entityManager, $this->mpStatsApi, $this->mpStatsApiOz, $this->mpStatsApiWb);
    }

    #[Route(path: '/item/{sku}', name: 'wb_item')]
    public function item($sku, Request $request): Response
    {
        $check = $this->checkStatusUser();
        if($check) return $check;
        return $this->render('wb/categoryItem.html.twig',
            $this->service->getItem($sku, $request->query->all())
        );
    }
    #[Route(path: '/ordersbyregionsize/{sku}', name: 'wb_order_by_region')]
    public function orderByRegion($sku, Request $request): Response
    {
        $check = $this->checkStatusUser();
        if($check) return $check;
        return $this->render('wb/order_by_region.html.twig',
            $this->service->getOrderByRegion($sku, $request->query->all())
        );
    }
    #[Route(path: '/keywords/{sku}', name: 'wb_keywords')]
    public function keywords($sku, Request $request): Response
    {
        $check = $this->checkStatusUser();
        if($check) return $check;
        return $this->render('wb/keywords.html.twig',
            $this->service->getKeywords($sku, $request->query->all())
        );
    }
    #[Route(path: '/category', name: 'wb_category')]
    public function category(Request $request): Response
    {
        $check = $this->checkStatusUser();
        if($check) return $check;
        $url = $request->query->all()['url']??null;
        return $this->render('wb/category'.($url != '' ? 'Sale': '').'.html.twig',
            $this->service->getCategory($url != ''?$url:null, $request->query->all())
        );
    }
    #[Route(path: '/bysearch', name: 'wb_by_search')]
    public function bySearch(): Response
    {
        $check = $this->checkStatusUser();
        if($check) return $check;
        return $this->render('wb/bysearch.html.twig');
    }
    #[Route(path: '/seller', name: 'wb_search_seller')]
    public function seller(): Response
    {
        $check = $this->checkStatusUser();
        if($check) return $check;
        return $this->render('wb/searchSeller.html.twig');
    }
    #[Route(path: '/seller/{sku}', name: 'wb_seller')]
    public function getSeller($sku, Request $request): Response
    {
        $check = $this->checkStatusUser();
        if($check) return $check;
        return $this->render('wb/seller.html.twig',
            $this->service->searchSeller($sku, $request->query->all())
        );
    }
    #[Route(path: '/similar', name: 'wb_similar')]
    public function similar(Request $request): Response
    {
        $check = $this->checkStatusUser();
        if($check) return $check;
        $sku = $request->query->all()['sku']??null;
        if($sku && is_numeric($sku)){
            return $this->render('wb/similarSale.html.twig',
                $this->service->similar($sku)
            );
        }
        return $this->render('wb/similar.html.twig');
    }
    #[Route(path: '/in_similar', name: 'wb_in_similar')]
    public function inSimilar(Request $request): Response
    {
        $check = $this->checkStatusUser();
        if($check) return $check;
        $sku = $request->query->all()['sku']??null;
        if($sku && is_numeric($sku)){
            return $this->render('wb/inSimilarSale.html.twig',
                $this->service->similar($sku)
            );
        }
        return $this->redirectToRoute('wb_similar');
    }
    #[Route(path: '/brand', name: 'wb_brand')]
    public function brand(): Response
    {
        $check = $this->checkStatusUser();
        if($check) return $check;
        return $this->render('wb/brand.html.twig');
    }
    #[Route(path: '/brand/{path}', name: 'wb_brand_sale')]
    public function brandSale(string $path): Response
    {
        $check = $this->checkStatusUser();
        if($check) return $check;
        return $this->render('wb/brandSale.html.twig',
            $this->service->searchBrand($path)
        );
    }
    #[Route(path: '/search', name: 'wb_search', methods: ["GET"])]
    public function search(): Response
    {
        $check = $this->checkStatusUser();
        if($check) return $check;
        return $this->render('wb/search.html.twig');
    }
    #[Route(path: '/search', name: 'wb_search_post', methods: ["POST"])]
    public function searchPost(Request $request): Response
    {
        $check = $this->checkStatusUser();
        if($check) return $check;
        $word = $request->request->all()['search'];
        $word = $this->service->search($word);
        if(!$word) return $this->redirectToRoute('wb_search');
        return $this->redirect('item/'.$word);
    }
    #[Route(path: '/top-brands', name: 'wb_top_brands')]
    public function topBrands(): Response
    {
        $check = $this->checkStatusUser();
        if($check) return $check;
        return $this->render('wb/top-brands.html.twig');
    }
    #[Route(path: '/top-sellers', name: 'wb_top_sellers')]
    public function topSellers(): Response
    {
        $check = $this->checkStatusUser();
        if($check) return $check;
        return $this->render('wb/top-sellers.html.twig');
    }
}