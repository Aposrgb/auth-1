<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route("/cabinet")]
class CabinetController extends AbstractController
{
    #[Route(path: '/summary', name: 'summary')]
    public function summary(): Response
    {
        return $this->render('cabinet/summary.html.twig');
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
    #[Route(path: '/connect', name: 'connect')]
    public function connect(): Response
    {
        return $this->render('cabinet/connect.html.twig');
    }

}