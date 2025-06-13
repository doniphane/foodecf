<?php

namespace App\Controller;

use App\Repository\DishRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

class HomeController extends AbstractController
{
    public function __construct(
        private readonly SerializerInterface $serializer
    ) {
    }

    #[Route('/', name: 'app_home')]
    public function index(Request $request, DishRepository $dishRepository): Response
    {
        $dishes = $dishRepository->findAll();

        $formattedDishes = json_decode(
            $this->serializer->serialize(
                $dishes,
                'json',
                ['groups' => 'dish:read']
            ),
            true
        );

        return $this->render('home/index.html.twig', [
            'dishes' => $formattedDishes,
            'search' => $request->query->get('search', ''),
            'category' => $request->query->get('category', 'all'),
            'sort' => $request->query->get('sort', 'default'),
            'displayCount' => (int) $request->query->get('display', 12),
            'dishesCount' => count($dishes)
        ]);
    }
}
