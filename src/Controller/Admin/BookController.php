<?php
// Déclaration du namespace pour organiser le code
namespace App\Controller\Admin;
//importer les classes nécessaires
use App\Entity\Book;
use App\Entity\User;
use App\Enum\BookStatusEnum;
use App\Repository\BookRepository;
use App\Entity\Author;
use App\Repository\AuthorRepository;
use App\Form\BookType;
use Doctrine\ORM\EntityManagerInterface;
//adaptateur de requête pour la pagination
use Pagerfanta\Doctrine\ORM\QueryAdapter;
//classe principale de la pagination
use Pagerfanta\Pagerfanta;
//contrôleur de base de Symfony
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
//class pour gérer les requêtes HTTP
use Symfony\Component\HttpFoundation\Request;
//class pour gérer les réponses HTTP
use Symfony\Component\HttpFoundation\Response;
//attribut pour définir les routes
use Symfony\Component\Routing\Attribute\Route;
//ajouter  la classe isGranted pour la sécurité
use Symfony\Component\Security\Http\Attribute\IsGranted;


//ce controller gère les actions d'administration liées aux livres
#[Route('/admin/book')]

//définition du contrôleur final, ne peut pas être étendu
final class BookController extends AbstractController
{
    //affiche la liste des livres paginée
    #[Route('', name: 'app_admin_book_index', methods: ['GET'])]
    public function index(Request $request, BookRepository $bookRepository): Response
    {
        //créé un constructeur de requête pour les livres, triés par titre
        $queryBuilder = $bookRepository->createQueryBuilder('b')
            ->orderBy('b.title', 'ASC');
        //configurer la pagination avec Pagerfanta
        $adapter = new QueryAdapter($queryBuilder);
        $pagerfanta = new Pagerfanta($adapter);
        $pagerfanta->setMaxPerPage(6);
        $pagerfanta->setCurrentPage($request->query->getInt('page', 1));
        //rend la vue des livres avec la pagination
        return $this->render('admin/book/index.html.twig', [
            'controller_name' => 'BookController',
            'books' => $pagerfanta,
        ]);
    }


    //méthode pour créer un livre ou éditer un livre
    //l'utilisateur doit avoir ce rôle pour accéder à cette méthode
    #[IsGranted('ROLE_AJOUT_DE_LIVRE')]
    #[Route('/new', name: 'app_admin_book_new', methods: ['GET', 'POST'])]
    #[Route('/{id}/edit', name: 'app_admin_book_edit', requirements: ['id' => '\d+'], methods: ['GET', 'POST'])]
    public function create(?Book $book, Request $request, EntityManagerInterface $manager): Response
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

        //si on a un objet book on est sur la page d'édition
        //sinon on est sur la page de création

        //si un livre est passé, mode édition, vérifie que l'utilisateur
        //est le créateur du livre
        if ($book) {
            //ici on ne demande ni un rôle ni un une information de connection
            //on vérifie book.is_creator
            //autrement dit on pose la question : est-ce que l'utilisateur
            //qui a créé le livre est celui connecté ?
            //Symfony a besoin d'un voter pour cela
            $this->denyAccessUnlessGranted('book.is_creator', $book);
        }
        // Initialise un nouveau livre si null (mode création)
        $book ??= new Book();
        //crée le formulaire associé au livre
        $form = $this->createForm(BookType::class, $book);
        //traite la soumission du formulaire
        $form->handleRequest($request);
        //vérifie si le formulaire a été soumis et est valide
        if ($form->isSubmitted() && $form->isValid()) {
            //on récupère le user courant
            $user = $this->getUser();
            //SI NOUVEAU LIVRE(pas d'id')ET UTILSATEUR VALIDE
            //le livre n'a pas d'id, cela veut dire qu'il n'est pas encore enregistré dans la BDD
            //on est donc un nouvel objet book
            //et si mon utilisateur est une instance de User
            if (!$book->getId() && $user instanceof User) {
                //alors on associe le User à l'objet book
                //ASSOCIE L'UTIILISATEUR COMME CREATEUR DU LIVRE
                $book->setCreatedBy($user);
            }

            //persiste et enregistre le livre en BDD
            $book = $form->getData();
            $manager->persist($book);
            $manager->flush();
            //redirige cer la liste des livres
            return $this->redirectToRoute('app_admin_book_index');
        }
        //affiche le formulaire de création ou d'édition
        return $this->render('admin/book/new.html.twig', [
            'form' => $form,
        ]);
    }
    //affiche les détails d'un livre spécifique
    #[Route('/{id}', name: 'app_admin_book_show', requirements: ['id' => '\d+'], methods: ['GET'])]
    public function show(?Book $book): Response
    {
        //rend la vue avec les détails du livre
        return $this->render('admin/book/show.html.twig', [
            'book' => $book,
        ]);
    }
}
