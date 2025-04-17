<?php

namespace App\Controller\Admin;

use App\Entity\Author;
use App\Form\AuthorType;
use App\Repository\AuthorRepository;
use Doctrine\ORM\EntityManagerInterface;

use Pagerfanta\Doctrine\ORM\QueryAdapter;
use Pagerfanta\Pagerfanta;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;


#[Route('/admin/author')]
final class AuthorController extends AbstractController
{

    // Afficher tous les auteurs, les auteurs vivants et les auteurs morts avec la pagination
    #[Route('', name: 'app_admin_author_index', methods: ['GET'])]
    public function index(Request $request, AuthorRepository $repository): Response
    {
        // Pagination pour tous les auteurs
        $queryBuilder = $repository->createQueryBuilder('a')
            ->orderBy('a.name', 'ASC'); // Tri par nom

        $adapter = new QueryAdapter($queryBuilder);
        $pagerfanta = new Pagerfanta($adapter);
        $pagerfanta->setMaxPerPage(5); // Nombre d'auteurs par page
        $pagerfanta->setCurrentPage($request->query->getInt('page', 1)); // Page actuelle depuis l'URL

        // Récupération des auteurs vivants et morts
        $livingAuthors = $repository->findLivingAuthors();
        $deadAuthors = $repository->findDeadAuthors();

        return $this->render('admin/author/index.html.twig', [
            'controller_name' => 'AuthorController',
            'authors' => $pagerfanta,  // Transmission de la pagination des auteurs
            'livingAuthors' => $livingAuthors,  // Auteurs vivants
            'deadAuthors' => $deadAuthors,  // Auteurs morts
        ]);
    }




    /*
    //afficher tous les auteurs & les auteurs vivants & les auteurs morts
    #[Route('', name: 'app_admin_author_index', methods: ['GET'])]
    public function index(AuthorRepository $repository): Response
    {
        //afficher tous les auteurs
        $authors = $repository->findAll();
        //afficher les auteurs vivants
        $livingAuthors = $repository->findLivingAuthors();
        //afficher les auteurs morts
        $deadAuthors = $repository->findDeadAuthors();

        return $this->render('admin/author/index.html.twig', [
            'controller_name' => 'AuthorController',
            'authors' => $authors,
            'livingAuthors' => $livingAuthors,
            'deadAuthors' => $deadAuthors,
        ]);
    }
*/
    //ajouter un auteur ou éditer un auteur
    #[Route('/new', name: 'app_admin_author_new', methods: ['GET', 'POST'])]

    #[Route('/{id}/edit', name: 'app_admin_author_edit', requirements: ['id' => '\d+'], methods: ['GET', 'POST'])]

    public function new(?Author $author, Request $request, EntityManagerInterface $manager): Response
    {
        $author ??= new Author(); //si l'auteur n'existe pas, on crée un nouvel auteur
        $form = $this->createForm(AuthorType::class, $author);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $manager->persist($author);
            $manager->flush();

            return $this->redirectToRoute('app_admin_author_index');
        }

        return $this->render('admin/author/new.html.twig', [
            'form' => $form,
        ]);
    }

    //voir les détails d'un auteur
    #[Route('/{id}', name: 'app_admin_author_show', requirements: ['id' => '\d+'], methods: ['GET'])]
    public function show(?Author $author): Response
    {
        return $this->render('admin/author/show.html.twig', [
            'author' => $author,

        ]);
    }
}
