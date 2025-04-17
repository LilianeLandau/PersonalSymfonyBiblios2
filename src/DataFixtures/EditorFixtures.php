<?php

namespace App\DataFixtures;

use App\Entity\Editor;
use App\Entity\Book;
use App\Entity\Author;
use App\Enum\BookStatus;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class EditorFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        // Liste des éditeurs
        $editors = [

            ['name' => 'Gallimard'],
            ['name' => 'Flammarion'],
            ['name' => 'Hachette'],
            ['name' => 'Actes Sud'],
            ['name' => 'Robert Laffont'],
            ['name' => 'Le Livre de Poche'],
            ['name' => 'Larousse']

        ];

        foreach ($editors as $editorData) {
            $editor = new Editor();
            $editor->setName($editorData['name']);
            $manager->persist($editor);
        }

        $manager->flush();
    }
}
