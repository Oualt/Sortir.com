<?php

namespace App\Controller;

use App\Entity\Sortie;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\UploadedFile;
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

        // Récupérer le fichier téléchargé
        $file = $request->files->get('photo');

        if ($file instanceof UploadedFile) {

                $newFileName = md5(uniqid()) . '.' . $file->guessExtension();
                $file->move('directory_to_upload', $newFileName);
                $newPhotoPath = $newFileName;
                $user->setImage($newPhotoPath);
            }


            // Mettre à jour les informations du profil de l'utilisateur
            $user->setPseudo($pseudo);
            $user->setNom($nom);
            $user->setPrenom($prenom);
            $user->setTelephone($telephone);
            $user->setEmail($email);


            // Enregistrer les modifications dans la base de données

            $entityManager = $this->managerRegistry->getManager();
            $entityManager->persist($user);
            $entityManager->flush();


            // Rediriger vers la page de détails du profil après la mise à jour
            $this->addFlash('success', 'Profil modifié avec succès !');
            return $this->redirectToRoute('app_profilDetails');
        }




//// Gérer le téléchargement de la nouvelle image de profil
//$file = $request->files->get('registration_form')['maPhoto'];
//if ($file) {
//    // Gérer le téléchargement de l'image
//    $fileName = $this->generateUniqueFileName().'.'.$file->guessExtension();
//    $file->move($this->getParameter('photos_directory'), $fileName);
//
//    // Mettre à jour le chemin de l'image dans l'entité utilisateur
//    $user->setImage($fileName);
//}


}