<?php

namespace App\Controller;

use App\Entity\Campus;
use App\Entity\Sortie;
use App\Entity\User;
use App\Entity\Etat;
use App\Repository\CampusRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;

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

        $form = $this->createFormBuilder()
            ->add('search_type', ChoiceType::class, [
                'choices' => [
                    'Sorties dont je suis l\'organisateur /trice' => 'organizer',
                    'Sorties auxquelles je suis inscrit/e' => 'registered',
                    'Sorties auxquelles je ne suis pas inscrit/e' => 'not_registered',
                    'Sorties passées' => 'past',
                ],
                'label_attr' => [
                    'class' => 'form-check-label',
                ],
                'expanded' => true,
                'multiple' => false,
                'placeholder' => '',
                'required' => false,
            ])
            ->add('search', TextType::class, [
                'attr' => [
                    'class' => 'barre',
                    'placeholder' => 'Rechercher...'
                ],
                'required' => false,
            ])
            ->add('campus', EntityType::class, [
                'class' => Campus::class,
                'label' => 'Campus',
                'choice_label' => 'nom',
                'required' => false,
                'query_builder' => function (CampusRepository $repository) {
                    return $repository->createQueryBuilder('campus')
                        ->orderBy('campus.nom', 'ASC');}
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Rechercher',
            ])
            ->getForm();

        $form->handleRequest($request);

        $queryBuilder = $doctrine
            ->getRepository(Sortie::class)
            ->createQueryBuilder('s')
            ->leftJoin('s.etat', 'e')
            ->leftJoin('s.users', 'u')
            ->andWhere('e.id != :etatTermineeId') // Exclure l'état "Terminée" (id = 5)
            ->setParameter('etatTermineeId', 5);

        if ($form->isSubmitted() && $form->isValid()) {
            $searchType = $form->getData()['search_type'];
            $search = $form->get('search')->getData();
            $campus = $form->get('campus')->getData();

            if ($campus) {
                $queryBuilder
                    ->andWhere('s.campus = :campus')
                    ->setParameter('campus', $campus);
            }

            switch ($searchType) {
                case 'organizer':
                    $sorties = $queryBuilder
                        ->andWhere('s.organisateur = :organisateur')
                        ->setParameter('organisateur', $this->getUser())
                        ->andWhere('s.etat = 1') // Filtrer les sorties en état 1 pour l'organisateur
                        ->getQuery()
                        ->getResult();
                    break;
                case 'registered':
                    $sorties = $queryBuilder
                        ->andWhere('u = :user')
                        ->setParameter('user', $this->getUser())
                        ->getQuery()
                        ->getResult();
                    break;
                case 'not_registered':
                    $sorties = $queryBuilder
                        ->andWhere('u != :user')
                        ->orWhere('u IS NULL')
                        ->setParameter('user', $this->getUser())
                        ->getQuery()
                        ->getResult();
                    break;
                case 'past':
                    $sorties = $queryBuilder
                        ->andWhere('e.id = 5')
                        ->getQuery()
                        ->getResult();
                    break;
                default:
                    $sorties = $queryBuilder
                        ->leftJoin('s.etat', 'e')
                        ->andWhere('LOWER(s.nom) LIKE :search')
                        ->andWhere('e.id != 1') // Exclure les sorties en état 1 pour les autres types de recherche
                        ->setParameter('search', '%'.strtolower($search).'%')
                        ->getQuery()
                        ->getResult();
                    break;
            }
        } else {
            $sorties = $queryBuilder
                ->getQuery()
                ->getResult();
        }

// Vérifier et mettre à jour l'état des sorties présentes depuis plus d'un mois
        $maintenant = new \DateTime();
        $etatTerminee = $doctrine->getRepository(Etat::class)->find(5); // Récupérer l'état "Terminée"

        foreach ($sorties as $sortie) {
            $dateCreation = $sortie->getDateHeureDebut();
            $intervalle = $dateCreation->diff($maintenant);

            if ($intervalle->m >= 1) {
                // Mettre à jour l'état de la sortie à "Terminée"
                $sortie->setEtat($etatTerminee);
                $this->entityManager->flush();

                $this->addFlash('warning', 'La sortie "'.$sortie->getNom().'" est terminée.');
            }
        }

        return $this->render('accueil.html.twig', [
            'form' => $form->createView(),
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