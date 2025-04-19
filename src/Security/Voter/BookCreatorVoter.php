<?php

namespace App\Security\Voter;

use App\Entity\Book;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class BookCreatorVoter extends Voter
{

    /**
     * @inheritDoc
     */

    //la méthode supports retourne true si le voter doit voter et false si le voter doit s'abstenir
    //quand voter : si on a passé l'attribute book.is_creator et que le sujet est une instance de Book 
    protected function supports(string $attribute, mixed $subject): bool
    {
        return 'book.is_creator' === $attribute && $subject instanceof Book;
    }

    /**
     * @inheritDoc
     */
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
        $user = $token->getUser();
        if (!$user instanceof User) {
            return false;
        }

        //dans les autres cas on utilise cette notation pour indique à l'éditeur de code 
        //que $subject est bien une instance de Book
        //on reoturne la vérification qui dit que le $user 
        //doit être le même que celui de $subject->getCreatedBy()


        /** @var Book $subject */
        return $user === $subject->getCreatedBy();
    }
}
