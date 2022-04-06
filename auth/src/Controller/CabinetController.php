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
        return $this->render('cabinet/summary.html');
    }
}