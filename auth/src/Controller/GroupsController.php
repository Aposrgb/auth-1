<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class GroupsController extends AbstractController
{
    #[Route(path: '/groups', name: 'groups')]
    public function keyword(): Response
    {
        $check = $this->checkStatusUser();
        if($check) return $check;
        return $this->render('groups/groups.html.twig');
    }
}