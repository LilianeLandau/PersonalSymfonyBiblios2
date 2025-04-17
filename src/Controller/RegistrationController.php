<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;

//ajouter la classe IsGranted pour la gestion des autorisations
use Symfony\Component\Security\Http\Attribute\IsGranted;

class RegistrationController extends AbstractController
{
    //seuls les utilisateurs ayant le rôle ROLE_ADMIN peuvent enregistrer
    //un nouvel utilisateur
    //restreindre l'accès à la route uniquement aux utilisateurs ayant le rôle ROLE_ADMIN
    #[IsGranted('ROLE_ADMIN')]
    //route pour la page de saisie d'un nouvel utilisateur
    #[Route('/admin/user/new', name: 'app_register')]
    public function register(Request $request, UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $entityManager): Response
    {
        //créer une nouvelle instance de User
        $user = new User();
        // Création du formulaire basé sur la classe RegistrationFormType, lié à l'objet $user
        $form = $this->createForm(RegistrationFormType::class, $user);
        // Traitement de la requête HTTP et association des données du formulaire avec l'objet $user
        $form->handleRequest($request);
        //si le formulaire est soumis et valide
        if ($form->isSubmitted() && $form->isValid()) {
            /** @var string $plainPassword */
            //récupération du mot de passe en clair depuis le formulaire
            $plainPassword = $form->get('plainPassword')->getData();
            // encode the plain password
            $user->setPassword($userPasswordHasher->hashPassword($user, $plainPassword));
            //enregistrer l'utilisateur dans la BDD
            $entityManager->persist($user);
            $entityManager->flush();

            // do anything else you need here, like send an email
            //redirection vers la route app_main après enregistrement réussi
            return $this->redirectToRoute('app_main');
        }
        // Si le formulaire n'est pas soumis ou n'est pas valide, affichage du formulaire à l'utilisateur
        return $this->render('registration/register.html.twig', [
            //passe du formulaire à la vue qu'il soit affiché
            'registrationForm' => $form,
        ]);
    }
}
