<?php

namespace App\DataFixtures;

use App\Entity\Book;
use App\Entity\Author;
use App\Entity\Editor; //importer Editor
use App\Enum\BookStatus; //importer BookStatus
use App\DataFixtures\EditorFixtures; //importer EditorFixtures


use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class BookFixtures extends Fixture implements DependentFixtureInterface
{

    //méthode exécutée pour charger les données dans la BDD
    //ObjectManager $manager : objet qui permet de manipuler les entités
    public function load(ObjectManager $manager): void
    {

        // Récupérer le repository des éditeurs
        //$editorRepository = $manager->getRepository(Editor::class);

        //tableau contenant les données des livres
        $books = [
            [
                'title' => 'Les Misérables',
                'isbn' => '9782253096344',
                'cover' => 'les-miserables.jpg',
                'edited_at' => '1862-04-15',
                'plot' => 'L\'histoire de Jean Valjean, un ancien forçat qui devient maire d\'une ville française sous une nouvelle identité.',
                'page_number' => 1232,
                'status'  => BookStatus::Available->value,
                'editor_name' => 'Gallimard',
                'author_ids' => [1] // ID de Victor Hugo dans la base de données
            ],
            [
                'title' => 'L\'Étranger',
                'isbn' => '9782070360024',
                'cover' => 'etranger.jpg',
                'edited_at' => '1942-05-19',
                'plot' => 'L\'histoire de Meursault, un Français d\'Algérie qui tue un Arabe sur une plage.',
                'page_number' => 184,
                'status'  => BookStatus::Available->value,
                'editor_name' => 'Flammarion',
                'author_ids' => [2] // ID d'Albert Camus
            ],
            [
                'title' => 'À la recherche du temps perdu',
                'isbn' => '9782070754921',
                'cover' => 'temps-perdu.jpg',
                'edited_at' => '1913-11-14',
                'plot' => 'Roman-fleuve qui explore les thèmes du temps, de la mémoire et de l\'expérience.',
                'page_number' => 3031,
                'status'  => BookStatus::Available->value,
                'editor_name'  => 'Hachette',
                'author_ids' => [3] // ID de Marcel Proust
            ],
            [
                'title' => 'Le Petit Prince',
                'isbn' => '9782070612758',
                'cover' => 'petit-prince.jpg',
                'edited_at' => '1943-04-06',
                'plot' => 'Un pilote échoué dans le désert rencontre un jeune prince venu d\'une autre planète.',
                'page_number' => 96,
                'status'  => BookStatus::Borrowed->value,
                'editor_name' => 'Larousse',
                'author_ids' => [7] // ID d'Antoine de Saint-Exupéry
            ],
            [
                'title' => '1984',
                'isbn' => '9782070368228',
                'cover' => '1984.jpg',
                'edited_at' => '1949-06-08',
                'plot' => 'Un monde totalitaire où la liberté et la vérité n\'existent plus, et où Big Brother surveille tout.',
                'page_number' => 438,
                'status' => BookStatus::Available->value, // Utilisation de l'enum avec ->value pour obtenir la chaîne 'available'
                'editor_name' => 'Robert Laffont',
                'author_ids' => [12] // ID de George Orwell
            ],

            [
                'title' => 'Les Fleurs du mal',
                'isbn' => '9782253009115',
                'cover' => 'fleurs-du-mal.jpg',
                'edited_at' => '1857-06-25',
                'plot' => 'Recueil de poèmes qui explore la dualité de l\'âme humaine et la beauté du mal.',
                'page_number' => 305,
                'status' => BookStatus::Available->value,
                'editor_name' => 'Larousse',
                'author_ids' => [8] // ID de Charles Baudelaire
            ],

            [
                'title' => 'Les Contemplations',
                'isbn' => '9782253009115',
                'cover' => 'contemplations.jpg',
                'edited_at' => '1856-10-20',
                'plot' => 'Recueil de poèmes de Victor Hugo qui explore la nature, l\'amour, la mort et la religion.',
                'page_number' => 352,
                'status'  => BookStatus::Borrowed->value,
                'editor_name' => 'Flammarion',
                'author_ids' => [1] // ID de Victor Hugo
            ],
        ];



        $editorRepository = $manager->getRepository(Editor::class);
        $authorRepository = $manager->getRepository(Author::class);



        //parcourir le tableau de livres
        foreach ($books as $bookData) {
            //créer une nouvelle instance de l'entité Book
            $book = new Book();
            // Configuration des propriétés simples du livre
            $book->setTitle($bookData['title'])
                ->setIsbn($bookData['isbn'])
                ->setCover($bookData['cover'])
                ->setEditedAt(new \DateTimeImmutable($bookData['edited_at'])) // Conversion de la date en objet DateTimeImmutable
                ->setPlot($bookData['plot'])
                ->setPageNumber($bookData['page_number'])
                ->setStatus(BookStatus::from($bookData['status'])); // Conversion de la chaîne en valeur de l'enum

            // Ajouter l'éditeur par son nom
            $editor = $editorRepository->findOneBy(['name' => $bookData['editor_name']]);
            if ($editor) {
                $book->setEditor($editor);
            }

            // Gestion de la relation ManyToMany avec les auteurs
            foreach ($bookData['author_ids'] as $authorId) {
                $author = $authorRepository->find($authorId);
                if ($author) {
                    $book->addAuthor($author);
                }
            }

            // Enregistrement du livre dans le gestionnaire d'entités
            $manager->persist($book);
        }

        // Move the flush outside the loop for better performance
        $manager->flush();
    }

    // This is the corrected method with proper return type declaration
    public function getDependencies(): array
    {
        return [
            EditorFixtures::class,  // Les éditeurs doivent être créés avant les livres
            AuthorFixtures::class,  // Les auteurs doivent être créés avant les livres
        ];
    }
}
