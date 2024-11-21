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
use Symfony\Component\HttpFoundation\Request;

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
    public function add(Request $request): JsonResponse
    {
        $donnees = json_decode($request->getContent(), true);

        $player = new Player();
        $player->setFirstName($donnees['firstName']);
        $player->setLastName($donnees['lastName']);
        
        if (isset($donnees['Team'])) {

            $team = $this->em->getRepository(Team::class)->find($donnees['Team']);
            if ($team) {
                $player->setTeam($team);
            }
        }

        $this->em->persist($player);
        $this->em->flush();
        
        return $this->json($player, 201);
    }

    #[Route('/players', methods: ['DELETE'])]
    public function remove(Request $request) {
        $id = $request->query->get("id");
        $player = $this->em->getRepository(Player::class)->find($id);
        $this->em->remove($player);
        $this->em->flush();
        
        return new JsonResponse($this->json(["Reponse"=>"Succès"]), 404, [], true);
    }

    #[Route('/api/players/{id}/modifier', name: 'players_edit')]
    public function  editPlayers(int $id, EntityManagerInterface $em): Response {

        $repository = $em->getRepository(Player::class);
        $player = $repository->find($id);

        $firstName = $request->query->get("firstName");
        $player->setFirstName($firstName);

        $repository = $em->getRepository(Player::class);
        $player = $repository->find($id);

        $lastName = $request->query->get("lastName");
        $player->setLastName($firstName);
    }
}

