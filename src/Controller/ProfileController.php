<?php

namespace App\Controller;

use App\Entity\Client;
use App\Form\ProfileDeleteType;
use App\Repository\ReservationRepository;
use App\Repository\UniteRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('IS_AUTHENTICATED_REMEMBERED')]
class ProfileController extends AbstractController
{
    #[Route('/profile', name: 'app_profile')]
    public function index(): Response
    {
        $user = $this->getUser();
        return $this->render('profile/index.html.twig', [
            'user'=>$user
        ]);
    }

    #[Route('/profile/delete', name: 'app_profile_delete')]
    public function delete(Request $request, EntityManagerInterface $entityManager, UserPasswordHasherInterface $userPasswordHasher, ReservationRepository $rr, UniteRepository $ur, TokenStorageInterface $tokenStorage): Response
    {
        $userInterface = $this->getUser();
        $user=$entityManager->getRepository(Client::class)->find($userInterface->getId());
        $form = $this->createForm(ProfileDeleteType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {

            $password = $form->get('password')->getData();
            if (!$userPasswordHasher->isPasswordValid($user, $password)){
                $form->get("password")->addError(new FormError("Le mot de passe n'est pas valide"));
            }

            if ($form->isValid()) {
                $tokenStorage->setToken(null);

                $reservations = $rr->findByUser($user);
                foreach ($reservations as $reservation){

                    $ur->UpdateByReservation($reservation);
                    $entityManager->remove($reservation);
                    $entityManager->flush();
                }
                $entityManager->remove($user);
                $entityManager->flush();

                // do anything else you need here, like send an email
                $this->addFlash('success', 'Le compte a bien été supprimé');

                return $this->redirectToRoute('app_home');
            }
        }
        return $this->render('profile/delete.html.twig', [
            'user'=>$user,
            'form'=>$form
        ]);
    }
}
