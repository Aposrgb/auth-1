<?php

namespace App\Controller;

use App\Entity\WbDataEntity\WbData;
use App\Entity\WbDataEntity\WbDataProperty;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route("/wb")]
class WbController extends AbstractController
{
    public function __construct(
        protected EntityManagerInterface $em
    )
    {
    }

    #[Route(path: '/category', name: 'wb_category')]
    public function category(): Response
    {
        $user = $this->getUser();
        $token = $user->getApiToken()->last();

        $wbData = $this
            ->em
            ->getRepository(WbData::class)
            ->findOneBy(['apiToken' => $token->getId()]);

        $stocks = $this
            ->em
            ->getRepository(WbDataProperty::class)
            ->getProperty('wbDataStock', $wbData->getId());

        $category = new ArrayCollection();
        $categories = [];
        foreach ($stocks as $stock) {
            $stock = json_decode($stock["property"], true);
            $data = new ArrayCollection(array_column($category->toArray(), 'name'));
            if(!$data->contains($stock["category"])){
                $category[] = [
                    "name" => $stock["category"],
                ];
            }
        }
        return $this->render('wb/category.html.twig',
            [
                "categories" => $category
            ]
        );
    }
    #[Route(path: '/bysearch', name: 'wb_by_search')]
    public function bySearch(): Response
    {
        return $this->render('wb/bysearch.html.twig');
    }
    #[Route(path: '/seller', name: 'wb_seller')]
    public function seller(): Response
    {
        return $this->render('wb/seller.html.twig');
    }
    #[Route(path: '/similar', name: 'wb_similar')]
    public function similar(): Response
    {
        return $this->render('wb/similar.html.twig');
    }
    #[Route(path: '/brand', name: 'wb_brand')]
    public function brand(): Response
    {
        return $this->render('wb/brand.html.twig');
    }
    #[Route(path: '/search', name: 'wb_search')]
    public function search(): Response
    {
        return $this->render('wb/search.html.twig');
    }
    #[Route(path: '/top-brands', name: 'wb_top_brands')]
    public function topBrands(): Response
    {
        return $this->render('wb/top-brands.html.twig');
    }
    #[Route(path: '/top-sellers', name: 'wb_top_sellers')]
    public function topSellers(): Response
    {
        return $this->render('wb/top-sellers.html.twig');
    }
}