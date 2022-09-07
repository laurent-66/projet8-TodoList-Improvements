<?php

namespace App\Services;

use App\Repository\TaskRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;

class AnonymousAttachedTask
{
    public function __construct(
        EntityManagerInterface $entityManager,
        TaskRepository $taskRepository,
        UserRepository $userRepository
    ) {
        $this->entityManager = $entityManager;
        $this->taskRepository = $taskRepository;
        $this->userRepository = $userRepository;
    }

    public function execute()
    {

        $tasksWithoutUser =  $this->taskRepository->findByUser(null);
        $userAnonymous = $this->userRepository->findOneByUsername('anonymous_user');

        foreach ($tasksWithoutUser as $task) {
            $task->setUser($userAnonymous);
            $this->entityManager->persist($task);
        }

        $this->entityManager->flush();
    }
}
