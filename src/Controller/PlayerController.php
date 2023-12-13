<?php

namespace App\Controller;

use App\Entity\Player;
use App\Form\PlayerType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class PlayerController extends AbstractController
{
    #[Route('/player', name: 'app_player')]
    public function index(): JsonResponse
    {
        return $this->json([
            'message' => 'Welcome to your new controller!',
            'path' => 'src/Controller/PlayerController.php',
        ]);
    }

    #[Route('/player/create', name: 'app_player_create', methods: ['POST'])]
    public function create(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $player = new Player();
        $formPlayer = $this->createForm(PlayerType::class, $player);
        $formPlayer->handleRequest($request);

        $formPlayer->submit($data);

        // Vérifiez si le formulaire est valide
        if ($formPlayer->isValid()) {
            // Enregistrez l'entité dans la base de données ou effectuez d'autres actions nécessaires
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($player);
            $entityManager->flush();

            // Retournez une réponse JSON en cas de succès
            return $this->json([
                'message' => 'Player created successfully',
                'player' => $player,
            ]);
        }
        return $this->json([
            'message' => 'Player not created',
            'player' => $player,
        ]);
    }
}
