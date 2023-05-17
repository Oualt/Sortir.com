<?php

namespace App\Controller;


use App\Entity\Campus;
use App\Entity\Sortie;
use App\Entity\Ville;
use App\Form\CampusType;
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

    #[Route("/sortie/create", name:"sortie_create")]
    public function create(
        Request $request,
        EntityManagerInterface $entityManager
        ): Response
    {
        $campus = new Campus();
        $sortie = new Sortie();
        $ville = new Ville();

        $sortieForm = $this->createForm(SortieType::class, $sortie);
        $campusForm = $this->createForm(CampusType::class, $campus);
        $villeForm = $this->createForm(VilleType::class, $ville);

        $sortieForm->handleRequest($request);
        if ($sortieForm->isSubmitted()){
            $entityManager->persist($sortie);
            $entityManager->flush();

            $this->addFlash('success', 'Sortie ajoutée avec succès !');
            return $this->redirectToRoute('sortie_details', ['id' => $sortie->getId()]);
        }


     return $this->render('create.html.twig', [
         'sortieForm' => $sortieForm->createView(),
         'campusForm'=>$campusForm->createView(),
         'villeForm'=>$villeForm->createView()

     ]);
    }
    #[Route("/sortie/details/{id}", name: "sortie_details")]
    public function details(int $id, SortieRepository $sortieRepository): Response
    {
        $sortie = $sortieRepository->find($id);

        return $this->render('sortie/details.html.twig',[
            'sortie' => $sortie
        ]);
    }

}