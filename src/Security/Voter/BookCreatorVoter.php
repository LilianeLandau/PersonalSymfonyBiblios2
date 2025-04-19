<?php
//déclaration du namespace pour organiser le code
namespace App\Security\Voter;

//importer les classes nécessaires
//l'entité Book qui sera vérifiée
use App\Entity\Book;
//l'entité User pour l'utilisateur connecté
use App\Entity\User;
///contient les informations d'authentification de l'utilisateur
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
//classe de base pour les Voters
use Symfony\Component\Security\Core\Authorization\Voter\Voter;


//voter personnalisé, étend la classe Voter de Symfony
class BookCreatorVoter extends Voter
{

    /**
     * @inheritDoc
     */
    //CETTE METHODE  supports() PERMET DE FILTRER LES CAS OU LE VOTER DOIT VOTER OU S'ABSTENIR
    //cette méthode reçoit l'attribut qui a été passé en premier argument à isGranted
    //ainsi qu'un éventuel second argument
    //elle renvoie true le voter doit voter et false s'il doit s'abstenir
    //la méthode supports retourne true si le voter doit voter et false si le voter doit s'abstenir
    //quand voter : si on a passé l'attribute book.is_creator et que le sujet est une instance de Book 

    // Ce Voter ne doit intervenir que si :
    // 1. L'attribut est exactement 'book.is_creator'
    // 2. Le sujet est une instance de Book

    protected function supports(string $attribute, mixed $subject): bool
    {
        return 'book.is_creator' === $attribute && $subject instanceof Book;
    }

    //cette annotation PHPDoc sert à indiquer explicitment
    //que la méthode hérite de la documentation de la classe parente
    //en effet, classe BookCeratorVoter étend la classe Voter de SYmfony
    //l'annotation @inheritDoc permet de dire que cette méthode supports()
    //est identique à la méthode de la classe parente Voter::supports()

    /**
     * @inheritDoc
     */

    //CETTE METHODE voteOnAttribute() PREND LA DECISION D'AUTORISER OU NON L'ACCES    
    //cette méthode reçoit l'attribut, le sujet éventuel ainsi que l'objet TokenInterface
    //qui contient l'utilisateur connecté
    //elle renvoie true si l'utilisateur connecté est le créateur du livre et false sinon
    //la logique de vérification
    //il faut récupérer l'instance de l'utilisateur connecté pour vérifier si c'est lui qui a créé le livre
    //cet utilisateur se trouve dans $token
    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {

        //on extrait l'utilisateur de $token
        //ce $token sera toujours là, même si on n'a pas d'utilisateur connecté
        //dans ce cas getUser() retourne null
        //donc si mon $user n'est pas une instance de User
        //on retourne false

        // Récupération de l'utilisateur à partir du token
        // Note: getUser() peut retourner null si l'utilisateur n'est pas connecté
        $user = $token->getUser();
        // Si l'utilisateur n'est pas une instance de User (soit null, soit autre chose)
        // On refuse immédiatement l'accès
        if (!$user instanceof User) {
            return false;
        }

        //dans les autres cas on utilise cette notation pour indique à l'éditeur de code 
        //que $subject est bien une instance de Book
        //on retourne la vérification qui dit que le $user 
        //doit être le même que celui de $subject->getCreatedBy()
        //il s'agit d'une notation PHPDoc  

        // Ici, on sait que $subject est un Book (grâce à la méthode supports())
        // On utilise une annotation de type pour aider l'IDE/éditeur de code     

        /** @var Book $subject */

        // La logique de décision :
        // On autorise l'accès seulement si l'utilisateur connecté ($user)
        // est le même que le créateur du livre ($subject->getCreatedBy())
        return $user === $subject->getCreatedBy();
    }
}
