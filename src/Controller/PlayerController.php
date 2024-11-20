<?php

namespace App\Controller;

use App\Entity\Player;
use App\Entity\Team;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use Doctrine\ORM\EntityManagerInterface;

#[Route('/api')]
class PlayerController extends AbstractController
{
    public function __construct(private EntityManagerInterface $em) {}

    #[Route('/players', name: 'joueurs', methods: ["GET"])]
    public function index(SerializerInterface $serializer): JsonResponse
    {
        $players = $this->em->getRepository(Player::class)->findAll();
        $playersJson = $serializer->serialize($players, "json", ["groups"=>"allplayers"]);
        return new JsonResponse($playersJson, Response::HTTP_OK, [], true);
    }

    #[Route('/players/{id}', methods: ['GET'])]
    public function create(Player $player): JsonResponse
    {
        return $this->json($player);
    }

    #[Route('/players', methods: ['POST'])]
    public function display(Player $player): JsonResponse
    {
        $donnees = json_decode($request->getContent(), true);

        $player = new Player();
        $player->setFirstName($donnees['FirstName']);
        $player->setLastName($donees['LastName']);
        
        if (isset($donnees['team_id'])) {

            $team = $this->em->getRepository(Team::class)->find($donnees['team_id']);
            if ($team) {
                $player->setTeam($team);
            }
        }

        $this->em->persist($player);
        $this->em->flush();
        
        return $this->json($player, 201);
    }
}

