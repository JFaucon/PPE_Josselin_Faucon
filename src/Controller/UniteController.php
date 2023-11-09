<?php

namespace App\Controller;

use App\Repository\BaieRepository;
use App\Repository\ReservationRepository;
use App\Repository\UniteRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('IS_AUTHENTICATED_REMEMBERED')]
class UniteController extends AbstractController
{
    #[Route('/unite', name: 'app_unite')]
    public function index(ReservationRepository $rr , BaieRepository $br, UniteRepository $ur): Response
    {
        $user = $this->getUser();
        $reservations = $rr->findByUser($user);
        $baies = $br->findByUser($user->getId());
        $unites = $ur->findByUser($user->getId());
        $displayUnite = [];
        foreach ($baies as $baie){
            foreach ($baie->getUnites() as $unite){
                if (in_array($unite->getId(),$unites)){
                    $displayUnite[] = true;
                }else{
                    $displayUnite[] = false;
                }
            }
        }


        return $this->render('unite/index.html.twig', [
            'reservations'=>$reservations,
            'baies'=>$baies,
            'displayUnite'=>$displayUnite
        ]);
    }

    #[Route('/unite/{idReservation}', name: 'app_unite_add_time')]
    public function addTime(EntityManagerInterface $entityManager,ReservationRepository $rr, int $idReservation): Response
    {
        $reservation = $rr->find($idReservation);
        $datedefin = clone $reservation->getEndDate(); // Créez une copie de la date de base pour éviter de la modifier.
        $datedefin->modify("+" . $reservation->getForfait()->getNbMonth() . " months");
        $reservation->setEndDate($datedefin);
        $entityManager->flush();


        return $this->redirectToRoute("app_unite");
    }
}
