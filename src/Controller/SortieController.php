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

        $sortieForm->handleRequest($request);
        if ($sortieForm->isSubmitted()) {

            $campus = $sortieForm->getData()->getCampus();
            // dd($campus);
            $sortie->setCampus($campus);
            $sortie->setOrganisateur($this->getUser());
            $ville = $sortieForm->get('ville')->getData();

            $etatRepository = $entityManager->getRepository(Etat::class);
            $etatCree = $etatRepository->findOneBy(['libelle' => 'enCreation']);
            // ajouter manuellement les états dans la base de données : enCreation, ouverte, cloturee, enCours, terminee, annulee
            $sortie->setEtat($etatCree);

            $lieu = $sortieForm->get('lieu')->getData();
            $sortie->setLieu($lieu);

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
    public function details(int $id, SortieRepository $sortieRepository): Response
    {
        $sortie = $sortieRepository->find($id);

        return $this->render('sortie/details.html.twig', [
            'sortie' => $sortie
        ]);
    }
}
