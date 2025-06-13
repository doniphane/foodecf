<?php

namespace App\Controller;

use App\Entity\Dish;
use App\Entity\User;
use App\Repository\DishRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/admin')]
#[IsGranted('ROLE_ADMIN')]
class AdminController extends AbstractController
{
    #[Route('/', name: 'app_admin_index')]
    public function index(UserRepository $userRepository, DishRepository $dishRepository): Response
    {
        return $this->render('admin/index.html.twig', [
            'users' => $userRepository->findAll(),
            'dishes' => $dishRepository->findAll(),
        ]);
    }

    #[Route('/users', name: 'app_admin_users')]
    public function users(UserRepository $userRepository): Response
    {
        return $this->render('admin/users.html.twig', [
            'users' => $userRepository->findAll(),
        ]);
    }

    #[Route('/dishes', name: 'app_admin_dishes')]
    public function dishes(DishRepository $dishRepository): Response
    {
        return $this->render('admin/dishes.html.twig', [
            'dishes' => $dishRepository->findAll(),
        ]);
    }

    #[Route('/user/{id}/delete', name: 'app_admin_user_delete', methods: ['POST'])]
    public function deleteUser(Request $request, User $user, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $user->getId(), $request->request->get('_token'))) {
            $entityManager->remove($user);
            $entityManager->flush();

            $this->addFlash('success', 'L\'utilisateur a été supprimé avec succès.');
        }

        return $this->redirectToRoute('app_admin_users');
    }

    #[Route('/dish/{id}/delete', name: 'app_admin_dish_delete', methods: ['POST'])]
    public function deleteDish(Request $request, Dish $dish, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $dish->getId(), $request->request->get('_token'))) {
            $entityManager->remove($dish);
            $entityManager->flush();

            $this->addFlash('success', 'Le plat a été supprimé avec succès.');
        }

        return $this->redirectToRoute('app_admin_dishes');
    }
}
