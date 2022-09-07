<?php

namespace App\Controller;

use DateTime;
use App\Entity\Task;
use App\Form\TaskType;
use App\Repository\TaskRepository;
use App\Repository\UserRepository;
use App\Services\FilterStatusTasks;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class TaskController extends AbstractController
{
    public function __construct(
        EntityManagerInterface $entityManager,
        TaskRepository $taskRepository,
        UserRepository $userRepository,
        Security $security
    ) {
        $this->entityManager = $entityManager;
        $this->taskRepository = $taskRepository;
        $this->userRepository = $userRepository;
        $this->security = $security;
    }


    #[Route('/admin/tasks', name: 'tasksUsersAnonymous')]
    public function listTasksUserAnonymous()
    {
        $userAnonymous = $this->userRepository->findOneByUsername('anonymous_user');
        $tasksPublic = $this->taskRepository->findByUser($userAnonymous);
        return $this->render('task/adminTasksAnonymous.html.twig', [
            'tasks' => $tasksPublic
        ]);
    }

    #[Route('/tasks/to_do', name: 'to-do_list')]
    public function listAction(): Response
    {
        if ($this->getUser()) {
            $tasks = $this->taskRepository->findByUser($this->getUser());
            $tasksTodo = FilterStatusTasks::filter($tasks, false);
            return $this->render('task/list.html.twig', [
                'tasks' => $tasksTodo
            ]);
        } else {
            $userAnonymous = $this->userRepository->findOneByUsername('anonymous_user');
            $tasksPublic = $this->taskRepository->findByUser($userAnonymous);
            $tasksPublicTodo = FilterStatusTasks::filter($tasksPublic, false);
            return $this->render('task/list.html.twig', [
                'tasks' => $tasksPublicTodo
            ]);
        }
    }

    #[Route('/tasks/completed', name: 'task_completed')]
    public function listTaskCompletedAction(): Response
    {
        if ($this->getUser()) {
            $tasks = $this->taskRepository->findByUser($this->getUser());
            $tasksCompleted = FilterStatusTasks::filter($tasks, true);
            return $this->render('task/listTaskCompleted.html.twig', [
                'tasks' => $tasksCompleted
            ]);
        } else {
            $userAnonymous = $this->userRepository->findOneByUsername('anonymous_user');
            $tasksPublic = $this->taskRepository->findByUser($userAnonymous);
            $tasksPublicIsDone = FilterStatusTasks::filter($tasksPublic, true);
            return $this->render('task/listTaskCompleted.html.twig', [
                'tasks' => $tasksPublicIsDone
            ]);
        }
    }


    #[Route("/tasks/create", name:"task_create")]
    public function createAction(Request $request)
    {
        $currentUser = $this->getUser();
        $userAnonymous = $this->userRepository->findOneByUsername('anonymous_user');
        $task = new Task();
        $form = $this->createForm(TaskType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $dataForm = $form->getData();
            $title = $dataForm->getTitle();
            $content = $dataForm->getContent();
            $task->setTitle($title);
            $task->setContent($content);
            $task->setIsDone(false);
            $task->setCreatedAt(new \DateTime());

            if ($currentUser) {
                $task->setUser($currentUser);
                $this->entityManager->persist($task);
                $this->entityManager->flush();
            } else {
                $task->setUser($userAnonymous);
                $this->entityManager->persist($task);
                $this->entityManager->flush();
            }
            $this->addFlash('success', 'La tâche a été bien été ajoutée.');
            return $this->redirectToRoute('to-do_list');
        }

        return $this->render('task/create.html.twig', ['form' => $form->createView()]);
    }


    #[Route("/tasks/{id}/edit", name:"task_edit")]
    public function editAction($id, Task $task, Request $request)
    {
        $task = $this->taskRepository->find($id);

        $form = $this->createForm(TaskType::class, $task);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->persist($task);
            $this->entityManager->flush();

            $this->addFlash('success', 'La tâche a bien été modifiée.');

            return $this->redirectToRoute('to-do_list');
        }

        return $this->render('task/edit.html.twig', [
            'form' => $form->createView(),
            'task' => $task,
        ]);
    }

    #[Route("/tasks/{id}/toggle", name:"task_toggle")]

    public function toggleTaskAction(Task $task)
    {
        $task->toggle(!$task->isIsDone());
        $this->entityManager->flush();
        if ($task->isIsDone()) {
            $this->addFlash('success', sprintf(
                'La tâche %s a bien été marquée comme faite.',
                $task->getTitle()
            ));
            return $this->redirectToRoute('to-do_list');
        } else {
            $this->addFlash('success', sprintf(
                'La tâche %s a bien été marquée comme non terminée.',
                $task->getTitle()
            ));
            return $this->redirectToRoute('task_completed');
        }
    }

    #[Route("/admin/tasks/{id}/delete", name:"task_anonymous_delete")]
    public function deleteTaskAnonymousAction(Task $task)
    {
        $this->entityManager->remove($task);
        $this->entityManager->flush();
        $this->addFlash('success', 'La tâche a bien été supprimée.');
        return $this->redirectToRoute('tasksUsersAnonymous');
    }

    #[Route("/tasks/{id}/delete", name:"task_delete")]
    public function deleteTaskToDoAction(Task $task)
    {
        if (!$task->isIsDone()) {
            $this->entityManager->remove($task);
            $this->entityManager->flush();
            $this->addFlash('success', 'La tâche a bien été supprimée.');
            return $this->redirectToRoute('to-do_list');
        } else {
            $this->entityManager->remove($task);
            $this->entityManager->flush();
            $this->addFlash('success', 'La tâche a bien été supprimée.');
            return $this->redirectToRoute('task_completed');
        }
    }
}
