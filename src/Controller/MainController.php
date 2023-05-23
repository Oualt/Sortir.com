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
    public function accueil(ManagerRegistry $doctrine)
    {
        $sorties = $doctrine
            ->getRepository(Sortie::class)
            ->createQueryBuilder('s')
            ->leftJoin('s.etat', 'e')
            ->leftJoin('s.users', 'u')
            ->getQuery()
            ->getResult();

        $searchForm = $this->createForm(SearchType::class);

        return $this->render('accueil.html.twig', [
            'form' => $searchForm->createView(),
            'sorties' => $sorties,
        ]);
    }

    #[Route("/search", name: "search_results")]
    public function searchResults(Request $request, SortieRepository $sortieRepository)
    {
        $searchTerm = $request->query->get('search');

        // Effectuer la recherche dans la base de données et récupérer les résultats
        $sorties = $sortieRepository->searchSorties($searchTerm);

        $searchForm = $this->createForm(SearchType::class);
        $form = $searchForm->createView();

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
