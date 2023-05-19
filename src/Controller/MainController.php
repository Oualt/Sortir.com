<?php

namespace App\Controller;

use App\Entity\Sortie;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @method getDoctrine()
 */
class MainController extends AbstractController
{
    #[Route("/", name: "main_login")]
    public function login()
    {
        return $this->redirectToRoute('app_login');
    }

    #[Route("/accueil", name: "main_accueil")]
    public function accueil(ManagerRegistry $doctrine)
    {
        $sorties = $doctrine->getRepository(Sortie::class)->createQueryBuilder('s')
            ->leftJoin('s.etat', 'e') // Jointure avec l'entité Etat
            ->getQuery()
            ->getResult();

        return $this->render('accueil.html.twig', ['sorties' => $sorties]);
    }

    #[Route("/DetailsProfil", name: "app_profilDetails")]
    public function detailsProfil()
    {
        return $this->render('detailsProfil.html.twig');
    }
    #[Route("/admin/villes", name: "app_villes")]
    public function villes()
    {
        return$this->render('villes.html.twig');
    }
    #[Route("/admin/campus", name: "app_campus")]
    public function campus()
    {
        return$this->render('campus.html.twig');
    }
}