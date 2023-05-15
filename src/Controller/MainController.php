<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class MainController extends AbstractController
{
    #[Route("/", name:"main_login")]
    public function login()
    {
        return $this->render('login.html.twig');
    }
    #[Route("/accueil", name:"main_accueil")]
    public function accueil()
    {
        return$this->render('accueil.html.twig');
    }
}