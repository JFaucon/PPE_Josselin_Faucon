<?php

namespace App\Controller;

use App\Entity\Report;
use App\Form\ReportType;
use App\Repository\ReportRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('IS_AUTHENTICATED_REMEMBERED')]
class ReportController extends AbstractController
{
    #[Route('/report', name: 'app_report')]
    public function index(Request $request,ReportRepository $rr, EntityManagerInterface $entityManager): Response
    {
        $report = new Report();
        $form = $this->createForm(ReportType::class, $report);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $report = $form->getData();
            $report->setUserr($this->getUser());

            $entityManager->persist($report);
            $entityManager->flush();


            $this->addFlash('success', 'Le report a bien été envoyé');
            return $this->redirectToRoute('app_home');
        }


        return $this->render('report/index.html.twig', [
            'form'=>$form
        ]);
    }
}
