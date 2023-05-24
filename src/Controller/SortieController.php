<?php

namespace App\Controller;


use App\Entity\Campus;
use App\Entity\Sortie;
use App\Entity\Lieu;
use App\Entity\Ville;
use App\Entity\Etat;
use App\Form\SortieType;
use App\Form\VilleType;
use App\Repository\SortieRepository;
use App\Repository\VilleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Constraints\DateTime;
use Symfony\Component\Validator\Constraints\DatetimeInterface;


class SortieController extends AbstractController
{
    //affichage du formulaire de création de sortie

    #[Route("/sortie/create", name: "sortie_create")]
    public function create(
        Request $request,
        EntityManagerInterface $entityManager
    ): Response {
        $campus = new Campus();
        $sortie = new Sortie();

        $sortieForm = $this->createForm(SortieType::class, $sortie);
        $action = $request->request->get('action');



        $sortieForm->handleRequest($request);
        if ($sortieForm->isSubmitted()) {

            $campus = $sortieForm->getData()->getCampus();
            // dd($campus);
            $sortie->setCampus($campus);
            $sortie->setOrganisateur($this->getUser());
            $ville = $sortieForm->get('ville')->getData();

            $etatRepository = $entityManager->getRepository(Etat::class);
            if ($action === 'enregistrer') {
                // Traitement pour le bouton "Enregistrer"
                $etatCree = $etatRepository->findOneBy(['libelle' => 'enCreation']);
            } elseif ($action === 'publier') {
                // Traitement pour le bouton "Publier la sortie"
                $etatCree = $etatRepository->findOneBy(['libelle' => 'ouverte']);
            } elseif ($action === 'annuler') {
                // Traitement pour le bouton "Annuler"
                $this->addFlash('failure', 'La sortie a bien été annulée');
                return $this->redirectToRoute('main_accueil', ['id' => $sortie->getId()]);
            }

            // ajouter manuellement les états dans la base de données : enCreation, ouverte, cloturee, enCours, terminee, annulee
            $sortie->setEtat($etatCree);

            $lieu = $sortieForm->get('lieu')->getData();
            $sortie->setLieu($lieu);

            $ville = $sortieForm->get('lieu')->getData()->getVille();
            $sortie->setVille($ville);

            // $sortie->setDateHeureDebut(new \DateTime());
            // $sortie->setDateLimiteInscription(new \DateTime());

            // $dateHeureDebut = $sortieForm->get('dateHeureDebut')->getData();
            // // dateHeureDebut to dateTimeInterface
            // //$dateHeureDebutFormatted = \DateTime::createFromFormat('Y-m-d H:i:s', $dateHeureDebut->format('YYYY-MM-DD hh:mm:ss'));
            // $dateHeureDebutFormatted = $dateHeureDebut->format('YYYY-MM-DD hh:mm:ss');
            // $sortie->setDateHeureDebut($dateHeureDebutFormatted);

            // $dateLimiteInscription = $sortieForm->get('dateLimiteInscription')->getData();
            // $dateLimiteInscriptionFormatted = $dateLimiteInscription->format('YYYY-MM-DD hh:mm:ss');
            // $sortie->setDateLimiteInscription($dateLimiteInscriptionFormatted);

            $entityManager->persist($sortie);
            $entityManager->flush();

            $this->addFlash('success', 'Sortie ajoutée avec succès !');
            return $this->redirectToRoute('sortie_details', ['id' => $sortie->getId()]);
        }


        return $this->render('create.html.twig', [
            'sortieForm' => $sortieForm->createView(),
        ]);
    }
    #[Route("/sortie/details/{id}", name: "sortie_details")]
    public function details(int $id, Request $request, EntityManagerInterface $entityManager, SortieRepository $sortieRepository): Response
    {
        $sortie = $sortieRepository->find($id);

        $action = $request->request->get('action');

        $etatRepository = $entityManager->getRepository(Etat::class);
        if ($action === 'publier') {
            // Traitement pour le bouton "Publier la sortie"
            $etatCree = $etatRepository->findOneBy(['libelle' => 'ouverte']);
            $sortie->setEtat($etatCree);
            $entityManager->flush();

            $this->addFlash('failure', 'La sortie a bien été annulée');
            return $this->redirectToRoute('main_accueil');
        } elseif ($action === 'annuler') {
            // Traitement pour le bouton "Annuler"
            $entityManager->remove($sortie);
            $entityManager->flush();

            $this->addFlash('failure', 'La sortie a bien été annulée');
            return $this->redirectToRoute('main_accueil');
        }

        $sortieForm = $this->createForm(SortieType::class, $sortie);

        return $this->render('sortie/details.html.twig', [
            'sortie' => $sortie,
            'user' => $this->getUser(),
            'sortieForm' => $sortieForm->createView()
        ]);
    }

    #[Route("/sortie/inscription/{id}", name: "sortie_inscription")]
    public function inscription(int $id, EntityManagerInterface $entityManager): Response
    {
        // Récupérer la sortie à laquelle l'utilisateur souhaite s'inscrire
        $sortie = $entityManager->getRepository(Sortie::class)->find($id);

        $sortie->addParticipant($this->getUser()); // Ajoute l'utilisateur actuel comme participant
        $entityManager->flush();

        $this->addFlash('success', 'Vous êtes inscrit à la sortie !');

        return $this->redirectToRoute('sortie_details', ['id' => $sortie->getId()]);
    }

    #[Route("/sortie/sortieModifDetails/{id}", name: "sortie_modifDetails")]
    public function modifDetails(int $id, Request $request, EntityManagerInterface $entityManager, SortieRepository $sortieRepository): Response
    {
        $sortie = $sortieRepository->find($id);

        $action = $request->request->get('action');

        // Récupérer les données soumises dans le formulaire
        $nom = $request->request->get('nom');
        $dateEtHeure = $request->request->get('dateEtHeure');
        $dateLimite = $request->request->get('dateLimite');
        $duree = $request->request->get('duree');
        $places = $request->request->get('places');
        $description = $request->request->get('description');
        $campus = $request->request->get('campus');
        $ville = $request->request->get('ville');
        $lieu = $request->request->get('lieu');


        // Mettre à jour les informations du profil de l'utilisateur
        $sortie->setNom($nom);
        $sortie->setDateHeureDebut($dateEtHeure);
        $sortie->setDateLimiteInscription($dateLimite);
        $sortie->setDuree($duree);
        $sortie->setNbInscriptionsMax($places);
        $sortie->setInfosSortie($description);
        $sortie->setCampus($campus);
        $sortie->setVille($ville);
        $sortie->setLieu($lieu);

        $etatRepository = $entityManager->getRepository(Etat::class);
        if ($action === 'publier') {
            // Traitement pour le bouton "Publier la sortie"
            $etatCree = $etatRepository->findOneBy(['libelle' => 'ouverte']);
            $sortie->setEtat($etatCree);
            $entityManager->flush();

            $this->addFlash('failure', 'La sortie a bien été annulée');
            return $this->redirectToRoute('main_accueil');
        } elseif ($action === 'annuler') {
            // Traitement pour le bouton "Annuler"
            $entityManager->remove($sortie);
            $entityManager->flush();

            $this->addFlash('failure', 'La sortie a bien été annulée');
            return $this->redirectToRoute('main_accueil');
        }

        $sortieForm = $this->createForm(SortieType::class, $sortie);

        return $this->render('sortie/details.html.twig', [
            'sortie' => $sortie,
            'sortieForm' => $sortieForm->createView()
        ]);
    }
}

