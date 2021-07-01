<?php

namespace App\Controller;

use App\Repository\TaskRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\SerializerInterface;
use Psr\Log\LoggerInterface;

/**
 * @Route("/api/task")
 */
class TaskController extends AbstractController
{
    private TaskRepository $repository;
    private SerializerInterface $serializer;
    private LoggerInterface $logger;

    public function __construct(
        TaskRepository $taskRepository,
        SerializerInterface $serializer,
        LoggerInterface $logger
    ) {
        $this->repository = $taskRepository;
        $this->serializer = $serializer;
        $this->logger = $logger;
    }

    /**
     * @Route("/", name="tasks", methods={"GET"})
     */
    public function tasks(): JsonResponse
    {
        $response = null;

        try {
            $tasks = $this->repository->findAll();

            $response = new JsonResponse(
                $this->serializer->serialize($tasks, 'json'),
                Response::HTTP_OK,
                [],
                true
            );

            $this->logActions('tasks');
        } catch (\Throwable $th) {
            $response = new JsonResponse(
                $th->getMessage(),
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }

        return $response;
    }

    /**
     * @Route("/mytasks", name="my_tasks", methods={"GET"})
     */
    public function myTasks(): JsonResponse
    {
        $response = null;

        try {
            $email = $this->getUser()->getUsername();
            $tasks = $this->repository->getUserTasks($email);

            $response = new JsonResponse(
                $this->serializer->serialize($tasks, 'json'),
                Response::HTTP_OK,
                [],
                true
            );

            $this->logActions('my_tasks');
        } catch (\Throwable $th) {
            $response = new JsonResponse(
                $th->getMessage(),
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }

        return $response;
    }

    /**
     * @Route("/detail/{id}", name="task_detail", methods={"GET"})
     */
    public function detail($id): JsonResponse
    {
        $response = null;

        try {
            $task = $this->repository->find($id);

            $response = new JsonResponse(
                $this->serializer->serialize($task, 'json'),
                Response::HTTP_OK,
                [],
                true
            );

            $this->logActions('task_detail', $id);
        } catch (\Throwable $th) {
            $response = new JsonResponse(
                $th->getMessage(),
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }

        return $response;
    }

    /**
     * @Route("/create", name="create_task", methods={"POST"})
     */
    public function create(Request $request): JsonResponse
    {
        $response = null;

        try {
            $data = json_decode($request->getContent(), true);

            $task = $this->repository->createTask($data);

            $response = new JsonResponse(
                $this->serializer->serialize($task, 'json'),
                Response::HTTP_CREATED,
                [],
                true
            );

            $this->logActions('create_task');
        } catch (\Throwable $th) {
            $response = new JsonResponse(
                $th->getMessage(),
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }

        return $response;
    }

    /**
     * @Route("/update", name="update_task", methods={"PUT"})
     */
    public function update(Request $request): JsonResponse
    {
        $response = null;

        try {
            $data = json_decode($request->getContent(), true);

            $task = $this->repository->updateTask($data);

            $response = new JsonResponse(
                $this->serializer->serialize($task, 'json'),
                Response::HTTP_CREATED,
                [],
                true
            );

            $this->logActions('update_task', $data['id']);
        } catch (\Throwable $th) {
            $response = new JsonResponse(
                $th->getMessage(),
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }

        return $response;
    }

    /**
     * @Route("/delete", name="delete_task", methods={"DELETE"})
     */
    public function delete(Request $request): JsonResponse
    {
        $response = null;

        try {
            $data = json_decode($request->getContent(), true);

            $task = $this->repository->deleteTask($data['id']);

            $response = new JsonResponse(
                $this->serializer->serialize($task, 'json'),
                Response::HTTP_OK,
                [],
                true
            );

            $this->logActions('delete_task', $data['id']);
        } catch (\Throwable $th) {
            $response = new JsonResponse(
                $th->getMessage(),
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }

        return $response;
    }

    /**
     * Logs the actions requested by the user
     *
     * @param string $action Controller action called
     * @param integer $id Id of the task (optional)
     *
     * @return void
     */
    private function logActions(string $action, int $id = 0): void
    {
        $user = $this->getUser()->getUsername();

        $message = self::class . ": $user requested action $action";

        if (!empty($id)) {
            $message .= " on entity id $id";
        }

        $this->logger->info($message);
    }
}
