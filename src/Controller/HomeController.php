<?php

namespace App\Controller;

use App\Entity\Forfait;
//use App\Service\Redis;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(EntityManagerInterface $entityManager): Response
    {
        /*$redisClient = $redis->getClient();
        $redisClient->hmset("player:1234",[
                "nom" => "joueur 1",
                "email" => "joueur1@test.com",
                "dateinscription" => "2024-01-24",
                "avatar" => "avatar1.png"
            ]);
        $redisClient->hmset("player:1235",[
            "nom" => "joueur 2",
            "email" => "joueur2@test.com",
            "dateinscription" => "2024-01-25",
            "avatar" => "avatar2.png"
        ]);
        $redisClient->hmset("player:1236",[
            "nom" => "joueur 3",
            "email" => "joueur3@test.com",
            "dateinscription" => "2024-01-26",
            "avatar" => "avatar3.png"
        ]);
        $redisClient->hmset("player:1237",[
            "nom" => "joueur 4",
            "email" => "joueur4@test.com",
            "dateinscription" => "2024-01-27",
            "avatar" => "avatar4.png"
        ]);
        $redisClient->hmset("player:1238",[
            "nom" => "joueur 5",
            "email" => "joueur5@test.com",
            "dateinscription" => "2024-01-28",
            "avatar" => "avatar5.png"
        ]);
        $redisClient->hmset("player:1239",[
            "nom" => "joueur 6",
            "email" => "joueur6@test.com",
            "dateinscription" => "2024-01-29",
            "avatar" => "avatar6.png"
        ]);
        $redisClient->zadd("leaderboard",[5000]);*/
        $forfaits = $entityManager->getRepository(Forfait::class)->findAll();
        return $this->render('home/index.html.twig', [
            'forfaits' => $forfaits,
        ]);
    }
}
