<?php

namespace App\Controller;

use App\Entity\Player;
use App\Entity\Team;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Doctrine\ORM\EntityManagerInterface;

#[Route('/api')]
class TeamController extends AbstractController
{
    public function __construct(private EntityManagerInterface $em) {}

    #[Route('/players', name: 'app_conference', methods: ["GET"])]
    public function index(): JsonResponse
    {
        $player = $this->em->getRepository(Player::class)->findAll();
        return new Response('Tournois');
    }
}
