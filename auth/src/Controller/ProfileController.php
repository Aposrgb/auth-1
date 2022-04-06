<?php

namespace App\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ProfileController extends AbstractController
{
    #[Route(path: '/lk', name: 'get_profile')]
    #[IsGranted("ROLE_USER")]
    public function getProfile(): Response
    {
        return $this->render('lk/lk.html.twig');
    }
}