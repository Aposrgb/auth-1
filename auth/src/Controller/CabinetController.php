<?php

namespace App\Controller;

use App\Entity\ApiToken;
use App\Service\CabinetWbService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route("/cabinet")]
class CabinetController extends AbstractController
{
    public function __construct(
        protected EntityManagerInterface $entityManager,
        protected CabinetWbService $cabinetWbService
    )
    {
    }

    #[Route(path: '/summary', name: 'summary')]
    public function summary(Request $request): Response
    {
        $query = $request->query->all();
        return $this->render('cabinet/summary.html.twig',
            $this->cabinetWbService->getWbData($this->getUser()->getId(), $query)
        );
    }
    #[Route(path: '/sales', name: 'sales')]
    public function sales(Request $request): Response
    {
        $query = $request->query->all();
        return $this->render('cabinet/sales.html.twig',
            $this->cabinetWbService->getOrders($this->getUser()->getId(), $query)
        );
    }
    #[Route(path: '/products', name: 'products')]
    public function products(Request $request): Response
    {
        $query = $request->query->all();
        return $this->render('cabinet/products.html.twig',
            $this->cabinetWbService->getProducts($this->getUser()->getId(), $query)
        );
    }
    #[Route(path: '/warehouses', name: 'warehouse')]
    public function warehouse(Request $request): Response
    {
        $query = $request->query->all();
        return $this->render('cabinet/warehouse.html.twig',
            $this->cabinetWbService->getWarehouses($this->getUser()->getId(), $query)
        );
    }
    #[Route(path: '/order-region', name: 'order-region')]
    public function orderRegion(Request $request): Response
    {
        $query = $request->query->all();
        return $this->render('cabinet/order-region.html.twig',
            $this->cabinetWbService->getOrderRegion($this->getUser()->getId(), $query));
    }
    #[Route(path: '/import/cost-price', name: 'cost-price')]
    public function costPrice(): Response
    {
        return $this->render('cabinet/cost-price.html.twig');
    }
    #[Route(path: '/compare', name: 'compare')]
    public function compare(): Response
    {
        return $this->render('cabinet/compare.html.twig');
    }
    #[Route(path: '/income-calc', name: 'income-calc')]
    public function incomeCalc(): Response
    {
        return $this->render('cabinet/income-calc.html.twig');
    }
    #[Route(path: '/weekly-reports', name: 'weekly-reports')]
    public function weeklyReports(Request $request): Response
    {
        $query = $request->query->all();
        return $this->render('cabinet/weekly-reports.html.twig',
            $this->cabinetWbService->getWeeklyReports($this->getUser()->getId(), $query));
    }
    #[Route(path: '/connect', name: 'connect', methods: ["GET"])]
    public function connect(Request $request): Response
    {
        $query = $request->query->all();
        $context = [
            'tokens' => $this->getUser()->getApiToken()
        ];
        if(key_exists("error", $query))
            $context  = array_merge($context, ['error' => $query["error"]]);

        return $this->render('cabinet/connect.html.twig',$context);
    }
    #[Route(path: '/connect', name: 'connect_post', methods: ["POST"])]
    public function connectAddToken(Request $request): Response
    {
        $key = $request->request->get('api_key');
        $name = $request->request->get('name');
        $error = $this->cabinetWbService->addApiToken($user = $this->getUser(), $name, $key);
        return $this->redirectToRoute('connect',
            [
                'tokens' => $user->getApiToken(),
                'error' => $error,
                'data' => $error == ''?$key:null
            ]
        );
    }
    #[Route(path: '/token/{id}', name: 'delete_token', methods: ["GET"])]
    public function deleteToken(ApiToken $token): Response
    {
        $this->cabinetWbService->deleteApiToken($token);
        return $this->json(["data" => ["messages" => "ok"]]);
    }
}