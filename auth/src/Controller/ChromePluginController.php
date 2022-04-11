<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class ChromePluginController extends AbstractController
{
    #[Route("/chrome-plugin", name: 'ch_plugin')]
    public function chromePlugin()
    {
        return $this->render('chrome/plugin.html.twig');
    }
}