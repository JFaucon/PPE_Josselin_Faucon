<?php

namespace App\Command;

use App\Entity\Unite;
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
                    $unitesAvailable = $UniteReservation->getUnites();
                    foreach ($unitesAvailable as $uniteAvailable) {
                        $uniteAvailable->setReservation(null);
                        $this->entityManager->flush();
                    }
                    $this->entityManager->remove($UniteReservation);
                    $this->entityManager->flush();
                    $available = true;
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
