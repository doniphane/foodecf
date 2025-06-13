<?php

namespace App\Controller;

use App\Entity\Dish;
use App\Entity\User;
use App\Form\DishType;
use App\Repository\DishRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;

#[Route('/admin')]
#[IsGranted('ROLE_ADMIN')]
class AdminController extends AbstractController
{
    #[Route('/', name: 'app_admin_index', methods: ['GET'])]
    public function index(DishRepository $dishRepository): Response
    {
        return $this->render('admin/index.html.twig', [
            'dishes' => $dishRepository->findAll(),
        ]);
    }

    #[Route('/users', name: 'app_admin_users', methods: ['GET'])]
    public function users(UserRepository $userRepository): Response
    {
        return $this->render('admin/users.html.twig', [
            'users' => $userRepository->findAll(),
        ]);
    }

    #[Route('/user/{id}/delete', name: 'app_admin_user_delete', methods: ['POST'])]
    public function deleteUser(
        Request $request,
        #[MapEntity(id: 'id')] User $user,
        EntityManagerInterface $entityManager
    ): Response {
        if ($this->isCsrfTokenValid('delete' . $user->getId(), $request->request->get('_token'))) {
            $entityManager->remove($user);
            $entityManager->flush();

            $this->addFlash('success', 'L\'utilisateur a été supprimé avec succès.');
        }

        return $this->redirectToRoute('app_admin_users');
    }

    #[Route('/dish/new', name: 'app_admin_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $dish = new Dish();
        $dish->setOwner($this->getUser());

        $form = $this->createForm(DishType::class, $dish);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($dish);
            $entityManager->flush();

            $this->addFlash('success', 'Le plat a été créé avec succès.');
            return $this->redirectToRoute('app_admin_index');
        }

        return $this->render('admin/new.html.twig', [
            'dish' => $dish,
            'form' => $form,
        ]);
    }

    #[Route('/dish/{id}', name: 'app_admin_show', methods: ['GET'])]
    public function show(
        #[MapEntity(id: 'id')] Dish $dish
    ): Response {
        return $this->render('admin/show.html.twig', [
            'dish' => $dish,
        ]);
    }

    #[Route('/dish/{id}/edit', name: 'app_admin_edit', methods: ['GET', 'POST'])]
    public function edit(
        Request $request,
        #[MapEntity(id: 'id')] Dish $dish,
        EntityManagerInterface $entityManager
    ): Response {
        $form = $this->createForm(DishType::class, $dish);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            $this->addFlash('success', 'Le plat a été modifié avec succès.');
            return $this->redirectToRoute('app_admin_index');
        }

        return $this->render('admin/edit.html.twig', [
            'dish' => $dish,
            'form' => $form,
        ]);
    }

    #[Route('/dish/{id}', name: 'app_admin_delete', methods: ['POST'])]
    public function delete(
        Request $request,
        #[MapEntity(id: 'id')] Dish $dish,
        EntityManagerInterface $entityManager
    ): Response {
        if ($this->isCsrfTokenValid('delete' . $dish->getId(), $request->request->get('_token'))) {
            $entityManager->remove($dish);
            $entityManager->flush();

            $this->addFlash('success', 'Le plat a été supprimé avec succès.');
        }

        return $this->redirectToRoute('app_admin_index');
    }
}
