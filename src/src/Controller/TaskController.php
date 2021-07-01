<?php

namespace App\Controller;

use App\Repository\TaskRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Task;
use Symfony\Component\HttpFoundation\Request;

/**
 * @Route("/api/task")
 */
class TaskController extends AbstractController
{
    private TaskRepository $repository;

    public function __construct(TaskRepository $taskRepository)
    {
        $this->repository = $taskRepository;
    }

    /**
     * @Route("/", name="tasks")
     */
    public function tasks(): JsonResponse
    {
        $tasks = $this->repository->findAll();

        $data = [];
        foreach ($tasks as $task) {
            $data[] = [
                'title' => $task->getTitle(),
                'description' => $task->getDescription(),
                'type' => $task->getType(),
                'priority' => $task->getPriority(),
                'assignee' => $task->getAssignee(),
                'status' => $task->getStatus(),
            ];
        }

        return new JsonResponse($data, Response::HTTP_OK);
    }

    /**
     * @Route("/create", name="create_task")
     */
    public function create(Request $request): JsonResponse
    {
        $task = new Task();

        $data = json_decode($request->getContent(), true);

        $task->setTitle($data['title']);
        $task->setDescription($data['description']);
        $task->setType($data['type']);
        $task->setPriority($data['priority']);
        $task->setAssignee($data['assignee']);
        $task->setStatus($data['status']);

        return new JsonResponse(json_encode($task), Response::HTTP_OK);
    }
}
