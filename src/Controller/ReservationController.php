<?php

namespace App\Controller;

use App\Entity\Forfait;
use App\Entity\Reservation;
use App\Entity\Unite;
use App\Form\ReservationType;
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
                }

                $entityManager->persist($reservation);
                $entityManager->flush();
                // do anything else you need here, like send an email

                return $this->redirectToRoute('app_home');
            }
        }elseif ($idForfait!= null){
            $selectedForfait = $entityManager->getRepository(Forfait::class)->find($idForfait);
            $reservation->setForfait($selectedForfait);
            $form = $this->createForm(ReservationType::class, $reservation);
        }

        return $this->render('reservation/index.html.twig', [
            'form' => $form,
        ]);
    }
}
