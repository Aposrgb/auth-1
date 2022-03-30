<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ProfileController extends AbstractController
{
    #[Route(path: '/profile', name: 'get_profile')]
    public function getProfile(): Response
    {
        return $this->render('lk/lk.html.twig');
    }
}