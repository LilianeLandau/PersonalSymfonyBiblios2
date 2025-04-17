<?php

namespace App\Repository;

use App\Entity\Author;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Author>
 */
class AuthorRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Author::class);
    }

    //afficher les auteurs vivants
    public function findLivingAuthors(): array
    {
        //Symfony crée un QueryBuilder, un OBJET permettant de construire des requêtes SQL
        //On appelle la méthode createQueryBuilder() sur $this, qui est l'instance de AuthorRepository
        //'a' est un alias pour la table Author 
        //(comme dans une requête SQL classique : SELECT * FROM author AS a)
        //cela signifie que toutes le colonnes de l'entité Author peuvent
        //être appelées avec a.nomDeLaColonne
        //la ligne ->getQuery() transforme le QueryBuilder en objet Query
        //la ligne->getQuery() transforme le DQL, Doctrine Query Language ,
        //écrit dans le QueryBuilder en SQL exécutable par la base de données
        //la ligne ->getResult() exécute la requête
        // et retourne un tableau d'objets Author
        //en résumé :
        //crée une requête SQL avec un alias 'a' pour l'entité Author
        //ajoute une condition WHERE pour ne sélectionner que les auteurs vivants
        //convertit la requête en objet Query
        //exécute la requête et retourne un tableau d'objets Author
        return $this->createQueryBuilder('a')
            ->andWhere('a.dateOfDeath IS NULL')
            ->orderBy('a.name', 'ASC')  // Tri par nom
            ->getQuery()
            ->getResult();
    }

    //afficher les auteurs morts
    public function findDeadAuthors(): array
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.dateOfDeath IS NOT NULL')
            ->orderBy('a.name', 'ASC')  // Tri par nom
            ->getQuery()
            ->getResult();
    }

    //    /**
    //     * @return Author[] Returns an array of Author objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('a')
    //            ->andWhere('a.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('a.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Author
    //    {
    //        return $this->createQueryBuilder('a')
    //            ->andWhere('a.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
