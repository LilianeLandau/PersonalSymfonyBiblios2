<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;


final class UsersController extends AbstractController
{
    #[Route('/users', name: 'app_users')]
    public function index(Request $request, UserRepository $userRepository): Response
    {

        $users = $userRepository->createQueryBuilder('u')
            ->orderBy('u.roles', 'ASC')
            ->getQuery()
            ->getResult();


        return $this->render('users/index.html.twig', [
            'controller_name' => 'UsersController',
            'users' => $users,
        ]);
    }
}
