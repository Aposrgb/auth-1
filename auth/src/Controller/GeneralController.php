<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class GeneralController extends AbstractController
{
    #[Route(path: '/index', name: 'index')]
    public function index(): Response
    {
        $check = $this->checkStatusUser();
        if($check) return $check;
        return $this->render('index/index.html.twig');
    }
}