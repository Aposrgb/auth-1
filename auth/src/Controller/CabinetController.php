<?php

namespace App\Controller;

use App\Entity\ApiToken;
use App\Entity\User;
use App\Entity\WbDataEntity\WbData;
use App\Entity\WbDataEntity\WbDataProperty;
use App\Service\CabinetWbService;
use App\Service\WbApiService;
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
    public function summary(): Response
    {
        $user = $this->getUser();
        $token = $user->getApiToken()->last();
        return $this->render('cabinet/summary.html.twig',
            $this->cabinetWbService->getWbData($token));
    }
    #[Route(path: '/sales', name: 'sales')]
    public function sales(): Response
    {
        return $this->render('cabinet/sales.html.twig');
    }
    #[Route(path: '/products', name: 'products')]
    public function products(): Response
    {
        return $this->render('cabinet/products.html.twig');
    }
    #[Route(path: '/warehouses', name: 'warehouse')]
    public function warehouse(): Response
    {
        return $this->render('cabinet/warehouse.html.twig');
    }
    #[Route(path: '/order-region', name: 'order-region')]
    public function orderRegion(): Response
    {
        return $this->render('cabinet/order-region.html.twig');
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
    public function weeklyReports(): Response
    {
        return $this->render('cabinet/weekly-reports.html.twig');
    }
    #[Route(path: '/connect', name: 'connect', methods: ["GET"])]
    public function connect(): Response
    {
        return $this->render('cabinet/connect.html.twig',
            ['tokens' => $this->getUser()->getApiToken()]
        );
    }
    #[Route(path: '/connect', name: 'connect_post', methods: ["POST"])]
    public function connectAddToken(Request $request, WbApiService $service): Response
    {
        $key = $request->request->get('api_key');
        $name = $request->request->get('name');
        $error = '';
        /** @var User $user */
        $user = $this->getUser();
        if(!$key || !$name){
            $error = "Не заполнено поле";
        }else if($key and $name){
            $token = $this->entityManager->getRepository(ApiToken::class)->findBy(['name' => $name]);
            $token = $token??$this->entityManager->getRepository(ApiToken::class)->findBy(['token' => $key]);
            if($token){
                $error = "Уже есть такой токен";
            }else{
                $user->addApiToken((new ApiToken())
                    ->setApiUser($user)
                    ->setName($name)
                    ->setToken($key)
                );
                $this->entityManager->flush();
                shell_exec("php ../bin/console wb:data:processing $key > /dev/null &");
            }
        }
        return $this->render('cabinet/connect.html.twig',
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
        $this->entityManager->remove($token);
        $wbData = $this->entityManager->getRepository(WbData::class)->findOneBy(["apiToken" => $token->getId()]);
        if($wbData){
            $this->entityManager->remove($wbData);
            $this->entityManager->getRepository(WbDataProperty::class)->removeAllProp($wbData->getId());
        }
        $this->entityManager->flush();
        return $this->json(["data" => ["messages" => "ok"]]);
    }
}