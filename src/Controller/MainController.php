<?php

namespace App\Controller;

use App\Entity\Sortie;
use App\Form\SearchType;
use App\Repository\SortieRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

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
    public function accueil(ManagerRegistry $doctrine, Request $request)
    {
        $queryBuilder = $doctrine
            ->getRepository(Sortie::class)
            ->createQueryBuilder('s')
            ->leftJoin('s.etat', 'e')
            ->leftJoin('s.users', 'u');

        $searchForm = $this->createForm(SearchType::class);
        $searchForm->handleRequest($request);

        if ($searchForm->isSubmitted() && $searchForm->isValid()) {
            $search = $searchForm->get('search')->getData();
            $sorties = $queryBuilder
                ->andWhere('LOWER(s.nom) LIKE :search')
                ->setParameter('search', '%'.strtolower($search).'%')
                ->getQuery()
                ->getResult();
        } else {
            $sorties = $queryBuilder
                ->getQuery()
                ->getResult();
        }

        return $this->render('accueil.html.twig', [
            'form' => $searchForm->createView(),
            'sorties' => $sorties,
        ]);
    }
    #[Route("/DetailsProfil", name: "app_profilDetails")]
    public function detailsProfil()
    {
        return $this->render('detailsProfil.html.twig');
    }
    #[Route("/admin/villes", name: "app_villes")]
    public function villes()
    {
        return $this->render('villes.html.twig');
    }
    #[Route("/admin/campus", name: "app_campus")]
    public function campus()
    {
        return $this->render('campus.html.twig');
    }
}
