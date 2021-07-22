<?php

namespace App\Controller;

use App\Entity\Task;
use App\Form\TaskType;
use App\Repository\TaskRepository;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TaskController extends AbstractController
{
    private $repository;
    private $manager;

    public function __construct(TaskRepository $repository, EntityManagerInterface $manager)
    {
        $this->repository = $repository;
        $this->manager = $manager;
    }

    /**
     * @Route("/tasks", name="task_list")
     *
     * @return Response
     */
    public function list(): Response
    {
        return $this->render('task/list.html.twig', [
            'tasks' => $this->repository->findBy(["isDone" => false]),
            'title' => "Tâches non terminées"
        ]);
    }

    /**
     * @Route("/tasks/done", name="task_done_list")
     *
     */
    public function isDoneList()
    {
        return $this->render('task/list.html.twig', [
            'tasks' => $this->repository->findBy(["isDone" => true]),
            'title' => "Tâches terminées"
            ]);
    }

    /**
     * @Route("/tasks/create", name="task_create")
     *
     * @param Request $request
     * @return Response
     */
    public function createTask(Request $request): Response
    {
        $task = new Task();
        $form = $this->createForm(TaskType::class, $task);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $task->setUser($this->getUser())
                ->setIsDone(0)
                ->setCreatedAt(new DateTime());
            $this->manager->persist($task);
            $this->manager->flush();

            $this->addFlash('success', 'La tâche a bien été ajoutée');

            return $this->redirectToRoute('task_list');
        }
        return $this->render('task/create.html.twig', [
            'form' => $form->createView()
        ]);
     }

    /**
     * @Route("/tasks/{id}/edit", name="task_edit")
     *
     * @param Task $task
     * @param Request $request
     * @return RedirectResponse|Response
     */
    public function editTask(Task $task, Request $request)
    {
        $form = $this->createForm(TaskType::class, $task);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->manager->flush();
            $this->addFlash('success', sprintf('La tâche %s a bien été modifiée.', $task->getTitle()));
            return $this->redirectToRoute('task_done_list');
        }
        return $this->render('task/edit.html.twig', [
            'form' => $form->createView(),
            'task' => $task
        ]);
    }

    /**
     * @Route("/tasks/{id}/toggle", name="task_toggle")
     *
     * @param Task $task
     * @return RedirectResponse
     */
    public function toggleTask(Task $task): RedirectResponse
    {
        $task->toggle(!$task->getIsDone());
        $this->manager->flush();

        $message = ($task->getIsDone() == true) ? "La tâche " . $task->getTitle() . " a bien été marquée comme terminée" : "La tâche " . $task->getTitle() . " a bien été marquée comme non terminée";

        $this->addFlash('success', $message);

        return $this->redirectToRoute('task_done_list');
    }

    /**
     * @Route("/tasks/{id}/delete", name="task_delete")
     *
     * @param Task $task
     * @return RedirectResponse
     */
    public function deleteTask(Task $task): RedirectResponse
    {
        if ($this->getUser() == $task->getUser() || $this->getUser()->getRoles() == "ROLE_ADMIN") {
            $this->manager->remove($task);
            $this->manager->flush();

            $this->addFlash('success', sprintf('La tâche %s a bien été supprimée.', $task->getTitle()));
            return $this->redirectToRoute('task_done_list');
        }

        $this->addFlash('error', 'Vous n\'avez pas les droits pour supprimer cette tâche.');
        return $this->redirectToRoute('task_done_list');
    }
}
