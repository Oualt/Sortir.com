<?php

namespace App\Controller;


use App\Entity\Campus;
use App\Entity\Sortie;
use App\Entity\Ville;
use App\Form\CampusType;
use App\Form\SortieType;
use App\Form\VilleType;
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
        $ville = new Ville();
        $sortieForm = $this->createForm(SortieType::class, $sortie);
        $campusForm = $this->createForm(CampusType::class, $campus);
        $villeForm = $this->createForm(VilleType::class, $ville);
            if ($ville instanceof Ville){
                $codePostal=$ville->getCodePostal();
            }

        //todo traiter le formulaire

     return $this->render('create.html.twig', [
         'sortieForm' => $sortieForm->createView(),
         'campusForm'=>$campusForm->createView(),
         'villeForm'=>$villeForm->createView()

     ]);
    }
}