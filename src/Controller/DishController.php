<?php

namespace App\Controller;

use App\Entity\Dish;
use App\Form\DishType;
use App\Repository\DishRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;

#[Route('/dish')]
#[IsGranted('ROLE_USER')]
class DishController extends AbstractController
{
    public function __construct(
        private readonly SerializerInterface $serializer
    ) {}

    #[Route('/', name: 'app_dish_index', methods: ['GET'])]
    public function index(DishRepository $dishRepository): Response
    {
        $dishes = $dishRepository->createQueryBuilder('d')
            ->leftJoin('d.owner', 'o')
            ->addSelect('o')
            ->where('d.owner = :user')
            ->setParameter('user', $this->getUser())
            ->getQuery()
            ->getResult();

        $formattedDishes = json_decode(
            $this->serializer->serialize(
                $dishes,
                'json',
                ['groups' => 'dish:read']
            ),
            true
        );

        return $this->render('dish/index.html.twig', [
            'dishes' => $formattedDishes,
        ]);
    }

    #[Route('/new', name: 'app_dish_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $dish = new Dish();
        $form = $this->createForm(DishType::class, $dish);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $dish->setOwner($this->getUser());
            $entityManager->persist($dish);
            $entityManager->flush();

            $this->addFlash('success', 'Le plat a été créé avec succès.');
            return $this->redirectToRoute('app_dish_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('dish/new.html.twig', [
            'dish' => $dish,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_dish_show', methods: ['GET'])]
    public function show(
        #[MapEntity(id: 'id')] Dish $dish
    ): Response {
        if (!$this->isGranted('ROLE_USER')) {
            throw $this->createAccessDeniedException('Vous devez être connecté.');
        }

        if ($dish->getOwner() !== $this->getUser()) {
            throw $this->createAccessDeniedException('Vous n\'êtes pas le propriétaire de ce plat.');
        }

        $formattedDish = json_decode(
            $this->serializer->serialize(
                $dish,
                'json',
                ['groups' => 'dish:read']
            ),
            true
        );

        return $this->render('dish/show.html.twig', [
            'dish' => $formattedDish,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_dish_edit', methods: ['GET', 'POST'])]
    public function edit(
        Request $request,
        #[MapEntity(id: 'id')] Dish $dish,
        EntityManagerInterface $entityManager
    ): Response {
        if (!$this->isGranted('ROLE_USER')) {
            throw $this->createAccessDeniedException('Vous devez être connecté.');
        }

        if ($dish->getOwner() !== $this->getUser()) {
            throw $this->createAccessDeniedException('Vous n\'êtes pas le propriétaire de ce plat.');
        }

        $form = $this->createForm(DishType::class, $dish);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            $this->addFlash('success', 'Le plat a été modifié avec succès.');
            return $this->redirectToRoute('app_dish_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('dish/edit.html.twig', [
            'dish' => $dish,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_dish_delete', methods: ['POST'])]
    public function delete(
        Request $request,
        #[MapEntity(id: 'id')] Dish $dish,
        EntityManagerInterface $entityManager
    ): Response {
        if (!$this->isGranted('ROLE_USER')) {
            throw $this->createAccessDeniedException('Vous devez être connecté.');
        }

        if ($dish->getOwner() !== $this->getUser()) {
            throw $this->createAccessDeniedException('Vous n\'êtes pas le propriétaire de ce plat.');
        }

        if ($this->isCsrfTokenValid('delete' . $dish->getId(), $request->request->get('_token'))) {
            $entityManager->remove($dish);
            $entityManager->flush();
            $this->addFlash('success', 'Le plat a été supprimé avec succès.');
        }

        return $this->redirectToRoute('app_dish_index', [], Response::HTTP_SEE_OTHER);
    }
}
