<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class GroupsController extends AbstractController
{
    #[Route(path: '/groups', name: 'groups')]
    public function keyword(): Response
    {
        return $this->render('groups/groups.html.twig');
    }
}