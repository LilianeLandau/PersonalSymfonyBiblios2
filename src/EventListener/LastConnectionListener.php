<?php

// Déclaration de l'espace de nom (namespace) pour cet écouteur d'événements
namespace App\EventListener;

// Importation des classes nécessaires
use App\Entity\User;
//interface principale de Doctrine ORM (Object-Relational Mapping) qui sert 
//d'intermédiaire entre l'application PHP et la base de données
use Doctrine\ORM\EntityManagerInterface;
//Un attribut PHP (introduit en PHP 8.0) qui permet de 
//déclarer une méthode comme écouteur d'événements de
// manière déclarative (sans configuration manuelle dans YAML/XML).
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
//Un événement déclenché par le composant Security de Symfony 
//lorsqu'un utilisateur se connecte via un 
//formulaire de login (connexion "interactive").
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;

// Déclaration de la classe de l'écouteur (Listener)
final class LastConnectionListener
{
    //INJECTION DE L'EntityManager VIA LE CONSTRUCTEUR
    // Constructeur avec injection de dépendance pour EntityManagerInterface
    // L'EntityManager permettra de sauvegarder les modifications en base de données
    public function __construct(private readonly EntityManagerInterface $manager) {}

    // Méthode écoutant l'événement 'security.interactive_login'
    // Attribut PHP8+ qui enregistre cette méthode comme écouteur d'événements
    // Ici on écoute l'événement 'security.interactive_login' qui est déclenché après une connexion réussie
    #[AsEventListener(event: 'security.interactive_login')]
    public function onSecurityInteractiveLogin(InteractiveLoginEvent $event): void
    {
        // Récupération de l'utilisateur à partir du token d'authentification
        $user = $event->getAuthenticationToken()->getUser();

        // Vérification que l'utilisateur est bien une instance de notre entité User
        // (pour éviter les erreurs si c'était un autre type d'utilisateur)
        if ($user instanceof User) {
            // Mise à jour de la date de dernière connexion avec la date/heure actuelle
            $user->setLastConnectedAt(new \DateTimeImmutable());

            // Sauvegarde des modifications en base de données
            $this->manager->flush();
        }
    }
}
