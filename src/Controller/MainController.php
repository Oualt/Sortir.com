<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class MainController extends AbstractController
{
    #[Route("/", name:"main_home")]
    public function login()
    {
        return $this->render('login.html.twig');
    }

    public function accueil()
    {
        return$this->
    }
}