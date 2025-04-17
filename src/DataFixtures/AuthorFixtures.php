<?php

namespace App\DataFixtures;

use App\Entity\Author;
use App\Entity\Book;
use App\Entity\Editor;
use App\Enum\BookStatus;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AuthorFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        // $product = new Product();
        // $manager->persist($product);

        $authors = [
            ['name' => 'Victor Hugo', 'dateOfBirth' => '1802-02-26', 'dateOfDeath' => '1885-05-22', 'nationality' => 'FR'],
            ['name' => 'Albert Camus', 'dateOfBirth' => '1913-11-07', 'dateOfDeath' => '1960-01-04', 'nationality' => 'FR'],
            ['name' => 'Marcel Proust', 'dateOfBirth' => '1871-07-10', 'dateOfDeath' => '1922-11-18', 'nationality' => 'FR'],
            ['name' => 'Émile Zola', 'dateOfBirth' => '1840-04-02', 'dateOfDeath' => '1902-09-29', 'nationality' => 'FR'],
            ['name' => 'Simone de Beauvoir', 'dateOfBirth' => '1908-01-09', 'dateOfDeath' => '1986-04-14', 'nationality' => 'FR'],
            ['name' => 'Jean-Paul Sartre', 'dateOfBirth' => '1905-06-21', 'dateOfDeath' => '1980-04-15', 'nationality' => 'FR'],
            ['name' => 'Antoine de Saint-Exupéry', 'dateOfBirth' => '1900-06-29', 'dateOfDeath' => '1944-07-31', 'nationality' => 'FR'],
            ['name' => 'Charles Baudelaire', 'dateOfBirth' => '1821-04-09', 'dateOfDeath' => '1867-08-31', 'nationality' => 'FR'],
            ['name' => 'William Shakespeare', 'dateOfBirth' => '1564-04-23', 'dateOfDeath' => '1616-04-23', 'nationality' => 'GB'],
            ['name' => 'Mark Twain', 'dateOfBirth' => '1835-11-30', 'dateOfDeath' => '1910-04-21', 'nationality' => 'US'],
            ['name' => 'J.K. Rowling', 'dateOfBirth' => '1965-07-31', 'dateOfDeath' => null, 'nationality' => 'GB'],
            ['name' => 'George Orwell', 'dateOfBirth' => '1903-06-25', 'dateOfDeath' => '1950-01-21', 'nationality' => 'GB'],
            ['name' => 'F. Scott Fitzgerald', 'dateOfBirth' => '1896-09-24', 'dateOfDeath' => '1940-12-21', 'nationality' => 'US'],
            ['name' => 'Gabriel García Márquez', 'dateOfBirth' => '1927-03-06', 'dateOfDeath' => '2014-04-17', 'nationality' => 'CO'],
            ['name' => 'Haruki Murakami', 'dateOfBirth' => '1949-01-12', 'dateOfDeath' => null, 'nationality' => 'JP']
        ];

        foreach ($authors as $authorData) {
            $author = new Author();
            $author->setName($authorData['name'])
                ->setDateOfBirth(new \DateTimeImmutable($authorData['dateOfBirth']))
                ->setDateOfDeath($authorData['dateOfDeath'] ? new \DateTimeImmutable($authorData['dateOfDeath']) : null)
                ->setNationality($authorData['nationality']);


            // Ajouter une référence pour le récupérer dans BookFixtures
            //  $referenceKey = 'author_' . strtolower(str_replace([' ', '.', '-'], '_', $authorData['name']));
            // $this->addReference($referenceKey, $author);



            $manager->persist($author);
        }


        $manager->flush();
    }
}
