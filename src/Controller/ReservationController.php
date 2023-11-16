<?php

namespace App\Controller;

use App\Entity\Forfait;
use App\Entity\Reservation;
use App\Entity\Unite;
use App\Form\EditReservationType;
use App\Form\ReservationType;
use App\Repository\ReservationRepository;
use App\Repository\UniteRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('IS_AUTHENTICATED_REMEMBERED')]
class ReservationController extends AbstractController
{
    #[Route('/reservation/{idForfait?}', name: 'app_reservation')]
    public function index(Request $request,UniteRepository $ur, EntityManagerInterface $entityManager,?int $idForfait): Response
    {
        $reservation = new Reservation();
        $form = $this->createForm(ReservationType::class, $reservation);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {

            $reservation = $form->getData();

            $reservation->setNumber(rand(100000,999999));
            $reservation->setUserr($this->getUser());
            $reservation->setBeginDate(new \DateTime('now'));
            $forfait = $reservation->getForfait();
            $datedefin = clone $reservation->getBeginDate(); // Créez une copie de la date de base pour éviter de la modifier.
            $datedefin->modify("+" . $forfait->getNbMonth() . " months");
            $reservation->setEndDate($datedefin);
            $unites = $ur->findByForfait($forfait->getNbSlot() * $reservation->getQuantity());

            if (count($unites)!=$forfait->getNbSlot() * $reservation->getQuantity()){
                $form->get("forfait")->addError(new FormError("vous ne pouvez pas commander autant d'unités pour le moment"));
            }
            if ($form->isValid()){

                foreach ($unites as $unite){
                    $reservation->addUnite($unite);
                    $unite->setAvailable(false);
                }

                $entityManager->persist($reservation);
                $entityManager->flush();
                // do anything else you need here, like send an email
                $this->addFlash('success', 'Achat validé! Vous pouvez constater vos unités dans "Mes Unités"');

                return $this->redirectToRoute('app_home');
            }
        }elseif ($idForfait!= null){
            $selectedForfait = $entityManager->getRepository(Forfait::class)->find($idForfait);
            $reservation->setForfait($selectedForfait);
            $form = $this->createForm(ReservationType::class, $reservation);
        }

        $forfaits = $entityManager->getRepository(Forfait::class)->findAll();

        return $this->render('reservation/index.html.twig', [
            'form' => $form,
            'forfaits'=>$forfaits
        ]);
    }

    #[Route('/reservation/edit/{idReservation?}', name: 'app_reservation_edit')]
    public function edit(Request $request,ReservationRepository $rr, EntityManagerInterface $entityManager,?int $idReservation): Response
    {
        $reservation = $rr->find($idReservation);
        $form = $this->createForm(EditReservationType::class, $reservation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $reservation = $form->getData();
            $entityManager->persist($reservation);
            $entityManager->flush();


            $this->addFlash('success', 'Les changements ont été effectués');
            return $this->redirectToRoute('app_home');
        }


        return $this->render('reservation/edit.html.twig', [
            'reservation'=>$reservation,
            'form'=>$form
        ]);
    }
}
