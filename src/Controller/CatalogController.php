<?php
// Déclaration du namespace pour organiser le code
namespace App\Controller;
//importer les classes nécessaires
use App\Entity\Book;
use App\Enum\BookStatusEnum;
use App\Repository\BookRepository;
use App\Entity\Author;
use App\Repository\AuthorRepository;
use App\Form\BookType;
use Doctrine\ORM\EntityManagerInterface;
use Pagerfanta\Doctrine\ORM\QueryAdapter;
use Pagerfanta\Pagerfanta;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class CatalogController extends AbstractController
{
    #[Route('/catalog', name: 'app_catalog')]
    public function index(Request $request, BookRepository $bookRepository): Response
    {

        $queryBuilder = $bookRepository->createQueryBuilder('b')
            ->orderBy('b.title', 'ASC');

        $adapter = new QueryAdapter($queryBuilder);
        $pagerfanta = new Pagerfanta($adapter);
        $pagerfanta->setMaxPerPage(6);
        $pagerfanta->setCurrentPage($request->query->getInt('page', 1));

        return $this->render('catalog/index.html.twig', [
            'controller_name' => 'CatalogController',
            'books' => $pagerfanta,
        ]);
    }

    //voir les détails d'un livre
    #[Route('/{id}', name: 'app_catalog_show', requirements: ['id' => '\d+'], methods: ['GET'])]
    public function show(?Book $book): Response
    {
        return $this->render('catalog/show.html.twig', [
            'book' => $book,
        ]);
    }
}
