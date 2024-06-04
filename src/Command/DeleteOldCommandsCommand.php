<?php
namespace App\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Commande;
use App\Repository\CommandeRepository;

class DeleteOldCommandsCommand extends Command
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;

        // Вызов конструктора родительского класса Command
        parent::__construct();
    }
    protected function configure()
    {
        $this->setName('app:delete-old-commands')
            ->setDescription('Delete old commands that are more than one day and 5 minutes old.');
    }
    protected function execute(InputInterface $input, OutputInterface $output ): int
    {
        $output->writeln([
            'Deleting all active commands...',
            '================================',
        ]);

        $repository = $this->entityManager->getRepository(Commande::class);
        $activeCommands = $repository->findBy(['statut_commande' => 'active']);

        foreach ($activeCommands as $commande) {
            $this->entityManager->remove($commande);
        }

        $this->entityManager->flush();

        $output->writeln('All active commands have been deleted.');

        return Command::SUCCESS;
    }  
}