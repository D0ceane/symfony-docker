<?php

namespace App\Controller;

use App\Entity\Member;
use App\Enum\Role;
use App\Form\MemberType;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\Entity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

//#[Route('/player', name: 'app_player')]
class MemberController extends AbstractController
{
    public function __construct(private readonly EntityManagerInterface $entityManager ) {}

    #[Route('/player/create', name: '_create', methods: ['POST', 'GET'], format: 'json')]
    public function create(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $player = new Member();
        $formPlayer = $this->createForm(MemberType::class, $player);
        $formPlayer->handleRequest($request);
        $formPlayer->submit($data);

        if ($formPlayer->isValid()) {
            $this->entityManager->persist($player);
            $this->entityManager->flush();

            return $this->json([
                'message' => 'Player created successfully',
                'name' => $player->getName(),
                'lastname' => $player->getLastname(),
                'pseudo' => $player->getPseudo(),
                'playingPosition' => $player->getPlayingPosition(),
            ], 200);
        }
        return $this->json([
            'message' => 'Player not created',
            'player' => $player,
        ], 404);
    }

    #[Route('/players', name: '_getPlayers', methods: ['GET'], format: 'json')]
    public function getPlayers(): JsonResponse
    {
        $players = $this->entityManager->getRepository(Member::class)->findAll();
        if (isset($players)) {
            $playersData = [];
            foreach ($players as $player) {
                $playersData[] = [
                    'name' => $player->getName(),
                    'lastname' => $player->getLastname(),
                    'pseudo' => $player->getPseudo(),
                    'playingPosition' => $player->getPlayingPosition(),
                ];
            }
            return $this->json([
                'message' => 'Players get successfully',
                'players' => $playersData,
            ], 200);
        }
        return $this->json([
            'message' => 'Players list does not exist',
        ], 404);
    }

    #[Route('/player/{id}', name: '_getPlayer', methods: ['GET'], format: 'json')]
    public function getPlayer(int $id): JsonResponse
    {
        $player = $this->entityManager->getRepository(Member::class)->find($id);
        if (isset($player)) {
            return $this->json([
                'message' => 'Player get successfully',
                'name' => $player->getName(),
                'lastname' => $player->getLastname(),
                'pseudo' => $player->getPseudo(),
                'playingPosition' => $player->getPlayingPosition(),
            ], 200);
        }
        return $this->json([
            'message' => 'Players list does not exist',
        ], 404);
    }

    #[Route('/player/update/{id}', name: '_patchPlayer', methods: ['PATCH'], format: 'json')]
    public function patchPlayer(Request $request, int $id): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $player = $this->entityManager->getRepository(Member::class)->find($id);
        if (array_key_exists('name', $data)) {
            $player->setName($data['name']);
        }
        if (array_key_exists('lastname', $data)) {
            $player->setLastname($data['lastname']);
        }
        if (array_key_exists('pseudo', $data)) {
            $player->setPseudo($data['pseudo']);
        }
        if (array_key_exists('playingPosition', $data)) {
            $player->setPlayingPosition($data['playingPosition']);
        }
        if (array_key_exists('role', $data)) {
            $role = $data['role'];
            $player->setRole(Role::fromValue($role));
        }

        if (!empty($player)) {
            $this->entityManager->persist($player);
            $this->entityManager->flush();

            return $this->json([
                'message' => 'Player get successfully',
                'name' => $player->getName(),
                'lastname' => $player->getLastname(),
                'pseudo' => $player->getPseudo(),
                'playingPosition' => $player->getPlayingPosition(),
                'role' => $player->getRole(),
            ], 200);
        }
        return $this->json([
            'message' => 'Players list does not exist',
        ], 404);
    }

    #[Route('/player/delete/{id}', name: '_deletePlayer', methods: ['DELETE'], format: 'json')]
    public function deletePlayer(int $id): JsonResponse
    {
            $player = $this->entityManager->getRepository(Member::class)->find($id);

            $this->entityManager->remove($player);
            $this->entityManager->flush();

            return $this->json([
                'message' => 'Player deleted successfully',
            ], 200);
    }
}
