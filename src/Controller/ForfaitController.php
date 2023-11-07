<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ForfaitController extends AbstractController
{
    #[Route('/forfait', name: 'app_forfait')]
    public function index(): Response
    {
        return $this->render('forfait/index.html.twig', [
            'controller_name' => 'ForfaitController',
        ]);
    }
}
