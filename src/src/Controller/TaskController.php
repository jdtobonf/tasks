<?php

/**
 * Task Controller Class
 */

namespace App\Controller;

use App\Repository\TaskRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\SerializerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * @package App\Controller
 * @author David Tobon <jdtobonf@gmail.com>
 *
 * @Route("/api/task")
 */
class TaskController extends AbstractController
{
    /**
     * @var TaskRepository $repository
     * Task repository to perform DB operations
     */
    private TaskRepository $repository;

    /**
     * @var SerializerInterface $serializer
     * The symfony serializer component
     */
    private SerializerInterface $serializer;

    /**
     * @var LoggerInterface $logger
     * The symfony logger component
     */
    private LoggerInterface $logger;

    /**
     * @var ValidatorInterface $validator
     * The symfony validator component
     */
    private ValidatorInterface $validator;

    /**
     * TaskController constructor
     *
     * @param TaskRepository      $taskRepository Task repository to perform DB operations
     * @param SerializerInterface $serializer     The symfony serializer component
     * @param LoggerInterface     $logger         The symfony logger component
     * @param ValidatorInterface  $validator      The symfony validator component
     */
    public function __construct(
        TaskRepository $taskRepository,
        SerializerInterface $serializer,
        LoggerInterface $logger,
        ValidatorInterface $validator
    ) {
        $this->repository = $taskRepository;
        $this->serializer = $serializer;
        $this->logger = $logger;
        $this->validator = $validator;
    }

    /**
     * Handles the task list endpoint
     *
     * @return JsonResponse
     *
     * @Route("/", name="tasks", methods={"GET"})
     */
    public function tasks(): JsonResponse
    {
        $response = null;

        try {
            $tasks = $this->repository->findAll();
            $jsonString = $this->serializer->serialize($tasks, 'json');

            $response = JsonResponse::fromJsonString($jsonString, Response::HTTP_OK);

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
     * Handles the task list endpoint for the current logged in user
     *
     * @return JsonResponse
     *
     * @Route("/mytasks", name="my_tasks", methods={"GET"})
     */
    public function myTasks(): JsonResponse
    {
        $response = null;

        try {
            $email = $this->getUser()->getUsername();
            $tasks = $this->repository->getUserTasks($email);
            $jsonString = $this->serializer->serialize($tasks, 'json');

            $response = JsonResponse::fromJsonString($jsonString, Response::HTTP_OK);

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
     * Handles the detail endpoint for a task
     *
     * @param int $id Symfony request object
     *
     * @return JsonResponse
     *
     * @Route("/detail/{id}", name="task_detail", methods={"GET"})
     */
    public function detail(int $id): JsonResponse
    {
        $response = null;

        try {
            $task = $this->repository->find($id);
            $jsonString = $this->serializer->serialize($task, 'json');

            $response = JsonResponse::fromJsonString($jsonString, Response::HTTP_OK);

            $this->logActions('task_detail', $id);
        } catch (NotFoundHttpException $th) {
            $response = new JsonResponse(
                $th->getMessage(),
                Response::HTTP_NOT_FOUND
            );
        } catch (\Throwable $th) {
            $response = new JsonResponse(
                $th->getMessage(),
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }

        return $response;
    }

    /**
     * Handles the create endpoint for tasks
     *
     * @param Request $request Symfony request object
     *
     * @return JsonResponse
     *
     * @Route("/create", name="create_task", methods={"POST"})
     */
    public function create(Request $request): JsonResponse
    {
        $response = null;

        try {
            $data = $request->toArray();
            $errorResponse = $this->validateRequestData($data);

            if (!empty($errorResponse)) {
                $response = $errorResponse;
            } else {
                $task = $this->repository->createTask($data);
                $jsonString = $this->serializer->serialize($task, 'json');

                $response = JsonResponse::fromJsonString($jsonString, Response::HTTP_CREATED);
            }

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
     * Handles the update endpoint for tasks
     *
     * @param Request $request Symfony request object
     *
     * @return JsonResponse
     *
     * @Route("/update", name="update_task", methods={"PUT"})
     */
    public function update(Request $request): JsonResponse
    {
        $response = null;

        try {
            $data = $request->toArray();
            $errorResponse = $this->validateRequestData($data);

            if (!empty($errorResponse)) {
                $response = $errorResponse;
            } else {
                $task = $this->repository->updateTask($data);
                $jsonString = $this->serializer->serialize($task, 'json');

                $response = JsonResponse::fromJsonString($jsonString, Response::HTTP_OK);
            }

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
     * Handles the delete endpoint for tasks
     *
     * @param Request $request Symfony request object
     *
     * @return JsonResponse
     *
     * @Route("/delete", name="delete_task", methods={"DELETE"})
     */
    public function delete(Request $request): JsonResponse
    {
        $response = null;

        try {
            $data = $request->toArray();

            $task = $this->repository->deleteTask($data['id']);
            $jsonString = $this->serializer->serialize($task, 'json');

            $response = JsonResponse::fromJsonString($jsonString, Response::HTTP_OK);

            $this->logActions('delete_task', $data['id']);
        } catch (NotFoundHttpException $th) {
            $response = new JsonResponse(
                $th->getMessage(),
                Response::HTTP_NOT_FOUND
            );
        } catch (\Throwable $th) {
            $response = new JsonResponse(
                $th->getMessage(),
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }

        return $response;
    }

    /**
     * Returns the validation constraints for a task
     *
     * @return Assert\Collection
     */
    private function getValidationConstraints(): Assert\Collection
    {
        return new Assert\Collection([
            'title' => [
                new Assert\NotBlank(),
                new Assert\Type(['type' => 'string']),
            ],
            'description' => new Assert\Optional([
                new Assert\NotBlank(),
                new Assert\Type(['type' => 'string']),
            ]),
            'type' => [
                new Assert\NotBlank(),
                new Assert\Type(['type' => 'string']),
            ],
            'priority' => [
                new Assert\NotBlank(),
                new Assert\Type(['type' => 'integer']),
                new Assert\Length(['min' => 1]),
            ],
            'assignee' => [
                new Assert\NotBlank(),
                new Assert\Email(),
            ],
            'status' => [
                new Assert\NotBlank(),
                new Assert\Type(['type' => 'string']),
            ]
        ]);
    }

    /**
     * Validated data based on contraints
     *
     * @param array $data Entity data to be validated
     *
     * @return JsonResponse|null
     */
    private function validateRequestData(array $data): ?JsonResponse
    {
        $constraint = $this->getValidationConstraints();

        $response = null;

        $errors = $this->validator->validate($data, $constraint);

        if (count($errors) > 0) {
            $response = new JsonResponse(
                $this->serializer->serialize($errors, 'json'),
                Response::HTTP_UNPROCESSABLE_ENTITY,
                [],
                true
            );
        }

        return $response;
    }

    /**
     * Logs the actions requested by the user
     *
     * @param string  $action Controller action called
     * @param integer $id     Id of the task (optional)
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
