<?php

namespace App\Controller;

use App\Entity\Sortie;
use App\Entity\User;
use App\Form\SearchType;
use App\Repository\SortieRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;

/**
 * @method getDoctrine()
 */
class MainController extends AbstractController
{
    private EntityManagerInterface $entityManager;
    private $managerRegistry;

    public function __construct(EntityManagerInterface $entityManager, ManagerRegistry $managerRegistry)
    {
        $this->entityManager = $entityManager;
        $this->managerRegistry = $managerRegistry;

    }


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

        $searchForm = $this->createForm(SearchType::class, null, [
            'entityManager' => $this->entityManager,
        ]);
        $searchForm->handleRequest($request);

        if ($searchForm->isSubmitted() && $searchForm->isValid()) {
            $search = $searchForm->get('search')->getData();
            $campus = $searchForm->get('campus')->getData();
            if ($campus) {
                $queryBuilder
                    ->andWhere('s.campus = :campus')
                    ->setParameter('campus', $campus);
            }
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


    #[Route("/DetailsProfil/{id}", name: "app_profilDetailsOrganisateur")]
    public function detailsProfilOrganisateur($id)
    {
        $o = $this->managerRegistry->getRepository(User::class)->find($id);

        if (!$o) {
            throw $this->createNotFoundException('L\'utilisateur avec l\'ID '.$id.' n\'existe pas.');
        }

        return $this->render('detailsProfilorganisateur.html.twig', [
            'organisateur' => $o,
        ]);
    }

    #[Route("/DetailsProfil/{id}", name: "app_profilDetailsParticipant")]
    public function detailsProfilParticipant($id)
    {
        $p = $this->managerRegistry->getRepository(User::class)->find($id);

        if (!$p) {
            throw $this->createNotFoundException('L\'utilisateur avec l\'ID '.$id.' n\'existe pas.');
        }

        return $this->render('detailsProfilParticipant.html.twig', [
            'participant' => $p,
        ]);
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
    #[Route("/UpdateProfil", name: "app_profilUpdate")]
    public function updateProfil()
    {
        return $this->render('updateUser.html.twig');
    }

}