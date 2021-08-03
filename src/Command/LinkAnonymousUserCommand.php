<?php

namespace App\Command;

use App\Repository\TaskRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class LinkAnonymousUserCommand extends Command
{
    protected static $defaultName = 'app:link-anonymous';
    private $entityManager;
    private $repository;
    private $userRepository;

    public function __construct(EntityManagerInterface $entityManager, TaskRepository $repository, UserRepository $userRepository, string $name = null)
    {
        parent::__construct($name);
        $this->entityManager = $entityManager;
        $this->repository = $repository;
        $this->userRepository = $userRepository;
    }

    protected function configure(): void
    {
        $this->setDescription('Link old unlink tasks.')
            ->setHelp('This command allows you to link old tasks to anonymous user...');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $unlinkTasks = $this->repository->findBy(["user" => null]);
        $anonymous = $this->userRepository->findOneBy(["username" => "anonyme"]);
        $output->writeln([
            'Link tasks to anonymous',
            '=======================',
            '',
        ]);
        foreach ($unlinkTasks as $task) {
            $task->setUser($anonymous);
            $this->entityManager->persist($task);
        }
        $this->entityManager->flush();
        $output->writeln("All unlink tasks are link to anonymous user.");
        return 0;
    }
}