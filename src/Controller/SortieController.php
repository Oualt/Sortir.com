<?php

namespace App\Controller;


use App\Entity\Campus;
use App\Entity\Sortie;
use App\Form\SortieType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SortieController extends AbstractController
{
    //affichage du formulaire de création de sortie

    #[Route("/sortie/create", name:"sortie_create")]
    public function create(Request $request): Response
    {
        $campus = new Campus();
        $sortie = new Sortie();
        $sortieForm = $this->createForm(SortieType::class, $sortie);
        $campusForm = $this->createForm(SortieType::class, $campus);

        //todo traiter le formulaire

     return $this->render('create.html.twig', [
         'sortieForm' => $sortieForm->createView(),
         'campusForm'=>$campusForm->createView()
     ]);
    }
}