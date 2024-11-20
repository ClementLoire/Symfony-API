<?php

namespace App\Controller;

use App\Entity\Team;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use Doctrine\ORM\EntityManagerInterface;


#[Route('/api')]
class TeamController extends AbstractController
{
    public function __construct(private EntityManagerInterface $em) {}

    #[Route('/teams', name: 'equipes', methods: ["GET"])]
    public function index(SerializerInterface $serializer): JsonResponse
    {
        $teams = $this->em->getRepository(Team::class)->findAll();
        $teamsJson = $serializer->serialize($teams, "json", ["groups=>allteams"]);
        return new JsonResponse($teamsJson, Response::HTTP_OK, [], true);
    }

    #[Route('/teams/{id}', methods: ['GET'])]
    public function create(Team $team): JsonResponse 
    {
        return $this->json($team);
    }

    #[Route('/teams', methods: ['POST'])]
    public function display(Request $request): JsonResponse
    {
        $donnees = json_decode($request->getContent(), true);

        $team = new Team();
        $team->setName($donnees['Name']);

        $this->em->persist($team);
        $this->em->flush();

        return $this->json($team, 201);
    }

    #[Route('/teams/{id}/players', methods: ['GET'])]
    public function playersTeam(Team $team): JsonResponse
    {
        $players = $this->em->getRepository(Player::class)->findBy(['team'=> $team]);
        return $this->json($players);
    }
}