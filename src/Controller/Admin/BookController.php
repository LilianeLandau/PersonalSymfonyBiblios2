<?php
// Déclaration du namespace pour organiser le code
namespace App\Controller\Admin;
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
//ajouter  la classe isGranted pour la sécurité
use Symfony\Component\Security\Http\Attribute\IsGranted;


//ce controller gère les actions d'administration liées aux livres
#[Route('/admin/book')]
final class BookController extends AbstractController
{
    //affiche la liste des livres paginée
    #[Route('', name: 'app_admin_book_index', methods: ['GET'])]
    public function index(Request $request, BookRepository $bookRepository): Response
    {
        $queryBuilder = $bookRepository->createQueryBuilder('b')
            ->orderBy('b.title', 'ASC');

        $adapter = new QueryAdapter($queryBuilder);
        $pagerfanta = new Pagerfanta($adapter);
        $pagerfanta->setMaxPerPage(6);
        $pagerfanta->setCurrentPage($request->query->getInt('page', 1));

        return $this->render('admin/book/index.html.twig', [
            'controller_name' => 'BookController',
            'books' => $pagerfanta,
        ]);
    }


    //méthode pour créer un livre
    //l'utilisateur doit avoir ce rôle pour accéder à cette méthode
    #[IsGranted('ROLE_AJOUT_DE_LIVRE')]
    #[Route('/new', name: 'app_admin_book_new', methods: ['GET', 'POST'])]
    public function create(Request $request, EntityManagerInterface $manager): Response
    {

        //on crée une instance du formulaire en utilisant la méthode createForm() de l'objet $this
        //on passe en paramètre de la méthode createForm() la classe du formulaire à instancier
        //on stocke l'instance du formulaire dans la variable $form
        //on utilise la méthode handleRequest() de l'objet $form pour traiter la requête HTTP
        //on vérifie si le formulaire a été soumis et si les données sont valides avec les méthodes isSubmitted() et isValid()
        //si le formulaire a été soumis et que les données sont valides, on récupère les données du formulaire avec la méthode getData() de l'objet $form
        //on utilise la méthode persist() de l'objet $manager pour préparer l'entité à être enregistrée en base de données
        //on utilise la méthode flush() de l'objet $manager pour enregistrer l'entité en base de données
        //on redirige l'utilisateur vers la liste des livres avec la méthode redirectToRoute() de l'objet $this
        //si le formulaire n'a pas été soumis ou que les données ne sont pas valides, on affiche le formulaire avec la méthode render() de l'objet $this
        $book = new Book();
        $form = $this->createForm(BookType::class, $book);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $book = $form->getData();
            $manager->persist($book);
            $manager->flush();

            return $this->redirectToRoute('app_admin_book_index');
        }

        return $this->render('admin/book/new.html.twig', [
            'form' => $form,
        ]);
    }
    //voir les détails d'un livre
    #[Route('/{id}', name: 'app_admin_book_show', requirements: ['id' => '\d+'], methods: ['GET'])]
    public function show(?Book $book): Response
    {
        return $this->render('admin/book/show.html.twig', [
            'book' => $book,
        ]);
    }
}
