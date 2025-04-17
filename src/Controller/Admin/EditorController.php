<?php

namespace App\Controller\Admin;

use App\Entity\Editor;
use App\Form\EditorType;
use App\Repository\EditorRepository;
use Doctrine\ORM\EntityManagerInterface;

use Pagerfanta\Doctrine\ORM\QueryAdapter;
use Pagerfanta\Pagerfanta;


use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;



#[Route('/admin/editor')]
final class EditorController extends AbstractController
{
    #[Route('', name: 'app_admin_editor_index', methods: ['GET'])]
    public function index(Request $request, EditorRepository $editorRepository): Response
    {
        $queryBuilder = $editorRepository->createQueryBuilder('e')
            ->orderBy('e.name', 'ASC');

        $adapter = new QueryAdapter($queryBuilder);
        $pagerfanta = new Pagerfanta($adapter);
        $pagerfanta->setMaxPerPage(5);
        $pagerfanta->setCurrentPage($request->query->getInt('page', 1));

        return $this->render('admin/editor/index.html.twig', [
            'controller_name' => 'EditorController',
            'editors' => $pagerfanta,
        ]);
    }

    #[Route('/new', name: 'app_admin_editor_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $manager): Response
    {
        $editor = new Editor();
        $form = $this->createForm(EditorType::class, $editor);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $manager->persist($editor);
            $manager->flush();

            return $this->redirectToRoute('app_admin_editor_index');
        }

        return $this->render('admin/editor/new.html.twig', [
            'form' => $form,
        ]);
    }

    //détails d'un éditeur
    #[Route('/{id}', name: 'app_admin_editor_show', requirements: ['id' => '\d+'], methods: ['GET'])]
    public function show(Editor $editor): Response
    {
        return $this->render('admin/editor/show.html.twig', [
            'editor' => $editor,
        ]);
    }
}
