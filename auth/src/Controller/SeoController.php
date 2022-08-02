<?php

namespace App\Controller;

use App\Service\SeoService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

#[Route("/seo")]
class SeoController extends AbstractController
{
    public function __construct(
        protected SeoService $service,
        protected UserPasswordHasherInterface $hasher,
        protected EntityManagerInterface $entityManager,
    )
    {
        parent::__construct($this->hasher, $this->entityManager, $this->mpStatsApi, $this->mpStatsApiOz, $this->mpStatsApiWb);
    }

    #[Route(path: '/keyword', name: 'keyword')]
    public function keyword(): Response
    {
        $check = $this->checkStatusUser();
        if($check) return $check;
        return $this->render('seo/keyword.html.twig');
    }
    #[Route(path: '/keyword/{name}', name: 'keyword_name')]
    public function keywordName(string $name): Response
    {
        $check = $this->checkStatusUser();
        if($check) return $check;
        if(strlen($name) == 0 ) return $this->redirectToRoute('keyword');
        return $this->render('seo/keywordName.html.twig',
            $this->service->getKeyword($name)
        );
    }
    #[Route(path: '/keywords/expanding', name: 'keywords_exp')]
    public function keywordsEx(): Response
    {
        $check = $this->checkStatusUser();
        if($check) return $check;
        return $this->render('seo/expanding.html.twig');
    }
    #[Route(path: '/keywords/expanding/{identity}', name: 'keywords_exp_identity')]
    public function getKeywordsEx($identity): Response
    {
        $check = $this->checkStatusUser();
        if($check) return $check;
        return $this->render('seo/expandingSale.html.twig',
            $this->service->getKeywordIdentity($identity)
        );
    }
    #[Route(path: '/keywords/expanding/keyword/{keyword}', name: 'keywords_exp_keyword')]
    public function getKeywordsExKey($keyword): Response
    {
        $check = $this->checkStatusUser();
        if($check) return $check;
        return $this->render('seo/expandingKeyword.html.twig',
            $this->service->getKeywordKey($keyword)
        );
    }
    #[Route(path: '/keywords/expanding/word/{keyword}', name: 'keywords_exp_word')]
    public function getKeywordsExWord($keyword): Response
    {
        $check = $this->checkStatusUser();
        if($check) return $check;
        return $this->render('seo/expandingKeyword.html.twig',
            $this->service->getKeywordWord($keyword)
        );
    }
    #[Route(path: '/keywords/selection', name: 'selection')]
    public function selection(Request $request): Response
    {
        $check = $this->checkStatusUser();
        if($check) return $check;
        return $this->render('seo/selection.html.twig',
            $this->service->selection($request->query->all())
        );
    }
    #[Route(path: '/position-tracking', name: 'position_tracking')]
    public function positionTracking(): Response
    {
        $check = $this->checkStatusUser();
        if($check) return $check;
        return $this->render('seo/position-tracking.twig');
    }
    #[Route(path: '/position-tracking/{sku}', name: 'position_tracking-sale')]
    public function positionTrackingSale($sku, Request $request): Response
    {
        $check = $this->checkStatusUser();
        if($check) return $check;
        $sale = $this->service->getPosition($sku);
        if(!$sale){
            return $this->redirectToRoute('position_tracking');
        }
        return $this->render('seo/position-tracking-sale.twig', $sale);
    }
    #[Route(path: '/tools/wb-card-checker', name: 'wb_card_checker')]
    public function toolsCardChecker(): Response
    {
        $check = $this->checkStatusUser();
        if($check) return $check;
        return $this->render('seo/wb-card-checker.html.twig');
    }
    #[Route(path: '/tools/wb-card-checker/{sku}', name: 'sku_checker', methods: ['GET'])]
    public function skuChecker($sku): Response
    {
        $check = $this->checkStatusUser();
        if($check) return $check;
        $sale = $this->service->getSku($sku);
        if(!$sale){
            return $this->redirectToRoute('wb_card_checker');
        }
        return $this->render('seo/skuChecker.html.twig', $sale);
    }
    #[Route(path: '/tools/wb-card-checker/{sku}', name: 'sku_checker_post', methods: ['POST'])]
    public function skuCheckerPost($sku, Request $request): Response
    {
        $check = $this->checkStatusUser();
        if($check) return $check;
        $sale = $this->service->getSkuPost($sku, $request->request->all());
        return $this->render('seo/skuChecker.html.twig', $sale);
    }
    #[Route(path: '/tools/wb-sku-compare', name: 'wb_sku_compare', methods: ['GET'])]
    public function toolsSkuCompare(): Response
    {
        $check = $this->checkStatusUser();
        if($check) return $check;
        return $this->render('seo/wb-sku-compare.html.twig');
    }
    #[Route(path: '/tools/wb-sku-compare', name: 'wb_sku_compare_post', methods: ['POST'])]
    public function toolsSkuComparePost(Request $request): Response
    {
        $check = $this->checkStatusUser();
        if($check) return $check;
        $groupA = $request->request->all()['groupA']??null;
        $groupB = $request->request->all()['groupB']??null;
        if($groupA && $groupB){
            if(strlen($groupB) > 0 &&  strlen($groupA) > 0){
                $compare = $this->service->compareResult($groupA, $groupB);
                if($compare){
                    return $this->render('seo/wb-sku-compare-result.html.twig', $compare);
                }
            }
        }
        return $this->redirectToRoute('wb_sku_compare');
    }
}