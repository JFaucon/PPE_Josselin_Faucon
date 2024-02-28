<?php

namespace App\Controller;

use App\Entity\Client;
use App\Form\ProfileDeleteType;
use App\Form\UserType;
use App\Repository\ReportRepository;
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
    public function index(Request $request, EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $file = $form['profileImage']->getData();
            $fileExtension = $file->guessExtension();
            if (!($fileExtension == 'png' || $fileExtension == 'jpg' || $fileExtension == 'gif')){
                $form->get("profileImage")->addError(new FormError("le fichier n'est pas valide"));
            }
            if ($form->isValid()){

                if ($file) {
                    $uploadsDirectory = $this->getParameter('uploads_directory');
                    $filename = md5(uniqid()) . '.' . $file->guessExtension();
                    $file->move(
                        $uploadsDirectory,
                        $filename
                    );

                    $user->setProfileImage($filename);
                }

                $entityManager->persist($user);
                $entityManager->flush();

                // Rediriger vers la page de profil ou une autre page après la mise à jour du profil
                return $this->redirectToRoute('app_profile');
            }
        }
        return $this->render('profile/index.html.twig', [
            'user'=>$user,
            'form'=>$form
        ]);
    }

    #[Route('/profile/delete', name: 'app_profile_delete')]
    public function delete(Request $request, EntityManagerInterface $entityManager, UserPasswordHasherInterface $userPasswordHasher, ReservationRepository $rr, UniteRepository $ur, TokenStorageInterface $tokenStorage , ReportRepository $rer): Response
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
                $reports = $rer->findByUser($user);
                foreach ($reports as $report){
                    $report->setUserr(null);
                    $entityManager->flush();
                }
                $base_dir = realpath($_SERVER["DOCUMENT_ROOT"]);
                $filePath = $base_dir.'/uploads/'.$user->getProfileImage();
                unlink($filePath);
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

    #[Route('/profile/imgdelete', name: 'app_profile_imgdelete')]
    public function imgdelete(EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();
        $base_dir = realpath($_SERVER["DOCUMENT_ROOT"]);
        $filePath = $base_dir.'/uploads/'.$user->getProfileImage();
        unlink($filePath);
        $user->setProfileImage(null);
        $entityManager->flush();

        return $this->redirectToRoute('app_profile');
    }
}
