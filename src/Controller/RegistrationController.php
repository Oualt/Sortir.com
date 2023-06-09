<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationFormType;
use App\Security\AppAuthenticator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\AccessDeniedException;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Security\Http\Authentication\UserAuthenticatorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class RegistrationController extends AbstractController
{
    #[Route('/register', name: 'app_register')]
    public function register(Request $request,
                             UserPasswordHasherInterface $userPasswordHasher,
                             UserAuthenticatorInterface $userAuthenticator,
                             AppAuthenticator $authenticator,
                             TokenStorageInterface $tokenStorage,
                             EntityManagerInterface $entityManager,
                             AuthorizationCheckerInterface $authorizationChecker): Response
    {
        $user = new User();
        $user->setRoles(['ROLE_USER']);
        $user->setAdministrateur(false);
        $user->setActif(true);
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // encode the plain password
           // dd($user);
            $plainPassword = $form->get('plainPassword')->getData();
    if ($plainPassword !== null) {
        $user->setPassword(
            $userPasswordHasher->hashPassword(
                $user,
                $form->get('plainPassword')->getData()
            )

        );
        // Récupérer le fichier de la photo de profil
        $photoFile = $form->get('maPhoto')->getData();

        // Vérifier si un fichier a été téléchargé
        if ($photoFile) {
            // Gérer le fichier, par exemple, déplacer le fichier vers un répertoire spécifique
            $newFilename = $timestamp = time(); // Timestamp actuel
            $extension = $photoFile->guessExtension(); // Obtenir l'extension du fichier d'origine
            $newFilename = $timestamp . '.' . $extension; // Combinaison du timestamp et de l'extension// Générer un nom de fichier unique
        try {
            $photoFile->move(
                'directory_to_upload', // Répertoire de destination
                $newFilename
            );
        } catch (FileException $e) {
            // Gérer les erreurs liées au téléchargement du fichier
        }

        // Mettre à jour le champ "maPhoto" de l'utilisateur avec le nom du fichier ou le chemin d'accès
        $user->setImage($newFilename);
    }

        $entityManager->persist($user);
        $entityManager->flush();

        if ($authorizationChecker->isGranted('IS_AUTHENTICATED_FULLY')) {
            throw new AccessDeniedException('This action is not allowed.');
        }

        $token = new UsernamePasswordToken($user, 'main', (array)'ROLE_USER', $user->getRoles());
        $tokenStorage->setToken($token);

        // message falsh a la creation du profil
        $this->addFlash('success', 'Profil créé avec succès !');
        return $this->redirectToRoute('main_accueil', ['id' => $user->getId()]);
    }
            return $userAuthenticator->authenticateUser(
                $user,
                $authenticator,
                $request
            );

    }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }
}
