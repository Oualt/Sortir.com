<?php

namespace App\Controller;

use App\Entity\Sortie;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends \Symfony\Bundle\FrameworkBundle\Controller\AbstractController
{
    private $managerRegistry;

    public function __construct(ManagerRegistry $managerRegistry)
    {
        $this->managerRegistry = $managerRegistry;
    }

    /**
     * @Route("/profile/update", name="update_profile", methods={"POST"})
     */
    public function updateProfile(Request $request,): Response
    {
        // Récupérer l'utilisateur actuellement connecté
        $user = $this->getUser();

        // Récupérer les données soumises dans le formulaire
        $pseudo = $request->request->get('pseudo');
        $nom = $request->request->get('nom');
        $prenom = $request->request->get('prenom');
        $telephone = $request->request->get('telephone');
        $email = $request->request->get('email');




        // Mettre à jour les informations du profil de l'utilisateur
        $user->setPseudo($pseudo);
        $user->setNom($nom);
        $user->setPrenom($prenom);
        $user->setTelephone($telephone);
        $user->setEmail($email);


        // Enregistrer les modifications dans la base de données
        $entityManager = $this->managerRegistry->getManager();
        $entityManager->flush();


        // Rediriger vers la page de détails du profil après la mise à jour
        $this->addFlash('success', 'Profil modifié avec succès !');
        return $this->redirectToRoute('app_profilDetails');
    }
}




