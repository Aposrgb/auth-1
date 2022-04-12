<?php

namespace App\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ProfileController extends AbstractController
{
    public function __construct(
        protected EntityManagerInterface $entityManager)
    {
    }

    #[Route(path: '/tarif', name: 'tarif')]
    public function tarif()
    {
        return $this->render('lk/tarif.html.twig');
    }
    #[Route(path: '/consulting', name: 'consulting')]
    public function consulting()
    {
        return $this->render('lk/consulting.html.twig');
    }
    #[Route(path: '/lk', name: 'get_profile')]
    public function getProfile(): Response
    {
        return $this->render('lk/lk.html.twig');
    }
    #[Route(path: '/bills', name: 'bills')]
    public function bills(): Response
    {
        return $this->render('lk/bills.html.twig');
    }
    #[Route(path: '/userpanel', name: 'userpanel')]
    public function userPanel(): Response
    {
        return $this->render('lk/userpanel.html.twig', [
            'user' => $this->getUser()
        ]);
    }
    #[Route(path: '/partner-stat', name: 'partner-stat')]
    public function partnerStat(): Response
    {
        return $this->render('lk/partner-stat.html.twig');
    }
    #[Route(path: 'user/report-history', name: 'report-history')]
    public function reportHistory(): Response
    {
        return $this->render('lk/report-history.html.twig');
    }
}