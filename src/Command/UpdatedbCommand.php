<?php

namespace App\Command;

use App\Entity\Unite;
use Mailjet\Resources;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Doctrine\ORM\EntityManagerInterface;

#[AsCommand(
    name: 'app:updatedb',
    description: 'Vérifie et met à jour la disponibilité des unités en fonction des réservations.',
)]
class UpdatedbCommand extends Command
{
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setName('app:updatedb')
            ->setDescription('Vérifie et met à jour la disponibilité des unités en fonction des réservations.')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $unites = $this->entityManager->getRepository(Unite::class)->findAll();

        foreach ($unites as $unite) {
            $UniteReservation = $unite->getReservation();
            if ($UniteReservation != null) {
                $available = false;
                if ($UniteReservation->getEndDate() < new \DateTime('now')) {
                    if ($UniteReservation->isRenewable()){
                        $dateDeFin = clone $UniteReservation->getEndDate();
                        $dateDeFin->modify("+" . $UniteReservation->getForfait()->getNbMonth() . " months");
                        $UniteReservation->setEndDate($dateDeFin);
                        $mj = new \Mailjet\Client('09bd773c9b8a9d62c4731f6a209c76c4','070e803f1b1dac2021b0c876c227fdd5',true,['version' => 'v3.1']);
                        $body = [
                            'Messages' => [
                                [
                                    'From' => [
                                        'Email' => "josselinfaucon@gmail.com",
                                        'Name' => "WorkTogether"
                                    ],
                                    'To' => [
                                        [
                                            'Email' => $this->getUser()->getEmail(),
                                            'Name' => $this->getUser()->getEmail()
                                        ]
                                    ],
                                    'Subject' => "Votre reservation Worktogether a été renouveller",
                                    'TextPart' => "Mailjet email",
                                    'HTMLPart' => "<h1>WorkTogether</h1><div>la reservation {$UniteReservation->getNumber()} a été mise a jour jusqu'au {$UniteReservation->getEndDate()}</p>Forfait : {$reservation->getForfait()->getName()}<br>Nombre d'unité : {$reservation->getForfait()->getNbSlot()}<br>Montant : ".$reservation->getForfait()->getPrice()/100*$reservation->getQuantity()."<p>Si vous avez des questions, n'hésitez pas à nous contacter.</p> <p>Merci encore pour votre achat !</p></div>",
                                    'CustomID' => "MailRenewable"
                                ]
                            ]
                        ];
                        $mj->post(Resources::$Email, ['body' => $body]);
                    }else{
                    $unitesAvailable = $UniteReservation->getUnites();
                    foreach ($unitesAvailable as $uniteAvailable) {
                        $uniteAvailable->setReservation(null);
                        $this->entityManager->flush();
                    }
                    $this->entityManager->remove($UniteReservation);
                    $this->entityManager->flush();
                    $available = true;
                    }
                }
            } else {
                $available = true;
            }

            if ($available) {
                $unite->setAvailable(true);
            } else {
                $unite->setAvailable(false);
            }
        }

        $this->entityManager->flush();
        $output->writeln('La commande a été exécutée avec succès.');

        return Command::SUCCESS;
    }

}
