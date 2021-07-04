<?php

/**
 * Test class for Task
 */

declare(strict_types=1);

namespace App\Tests;

use PHPUnit\Framework\TestCase;
use App\Entity\Task;
use App\Repository\TaskRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\Mapping\ClassMetadata;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * @package App\Tests
 * @author David Tobon <jdtobonf@gmail.com>
 */
final class TaskRepositoryTest extends TestCase
{
    /**
     * @var ?TaskRepository $taskRepository
     * Mocked TaskRepository
     */
    private ?TaskRepository $taskRepository;

    /**
     * @var ?ManagerRegistry $registry
     * Mocked ManagerRegistry
     */
    private ?ManagerRegistry $registry;

    /**
     * @var ?EntityManagerInterface $entityManager
     * Mocked EntityManagerInterface
     */
    private ?EntityManagerInterface $entityManager;

    /**
     * Sets the data and mocked objects for testing
     *
     * @return void
     */
    public function setUp(): void
    {
        $this->registry = $this->getMockBuilder(ManagerRegistry::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->entityManager = $this->getMockBuilder(EntityManagerInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->registry->method('getManagerForClass')
            ->willReturn($this->entityManager);

        $this->entityManager->method('getClassMetadata')
            ->willReturn(new ClassMetadata(Task::class));

        $this->entityManager->method('persist');
        $this->entityManager->method('remove');
        $this->entityManager->method('flush');
        $this->entityManager->method('find')
            ->will(
                $this->onConsecutiveCalls(
                    $this->getDummyTask(),
                    null
                )
            );

        $this->taskRepository = new TaskRepository($this->registry);
    }

    /**
     * Returns a dummy task
     *
     * @return Task
     */
    private function getDummyTask(): Task
    {
        $title = 'Title';
        $description = "description";
        $type = "type";
        $priority = 2;
        $assignee = "test@email.com";
        $status = "TO_DO";

        $task = new Task();
        $task->setTitle($title);
        $task->setDescription($description);
        $task->setType($type);
        $task->setPriority($priority);
        $task->setAssignee($assignee);
        $task->setStatus($status);

        return $task;
    }

    /**
     * Resets the data and mocked objects for testing
     *
     * @return void
     */
    public function tearDown(): void
    {
        $this->taskRepository = null;
    }

    /**
     * testCreateTaskReturnCreatedTask
     *
     * @return void
     */
    public function testCreateTaskReturnCreatedTask(): void
    {
        $title = 'My Task Title';
        $description = "My task description";
        $type = "My task type";
        $priority = 2;
        $assignee = "test@email.com";
        $status = "DONE";

        $task = $this->taskRepository->createTask([
            'title' => $title,
            'description' => $description,
            'type' => $type,
            'priority' => $priority,
            'assignee' => $assignee,
            'status' => $status,
        ]);

        $this->assertSame($title, $task->getTitle());
        $this->assertSame($description, $task->getDescription());
        $this->assertSame($type, $task->getType());
        $this->assertSame($priority, $task->getPriority());
        $this->assertSame($assignee, $task->getAssignee());
        $this->assertSame($status, $task->getStatus());
    }

    /**
     * testUpdateTaskReturnUpdatedTask
     *
     * @return void
     */
    public function testUpdateTaskReturnUpdatedTask(): void
    {
        $title = 'My Task Title';
        $description = "My task description";
        $type = "My task type";
        $priority = 2;
        $assignee = "test@email.com";
        $status = "DONE";

        $task = $this->taskRepository->updateTask([
            'id' => 1,
            'title' => $title,
            'description' => $description,
            'type' => $type,
            'priority' => $priority,
            'assignee' => $assignee,
            'status' => $status,
        ]);

        $this->assertSame($title, $task->getTitle());
        $this->assertSame($description, $task->getDescription());
        $this->assertSame($type, $task->getType());
        $this->assertSame($priority, $task->getPriority());
        $this->assertSame($assignee, $task->getAssignee());
        $this->assertSame($status, $task->getStatus());
    }

    /**
     * testUpdateTaskThrowsExceptionWhenNotFound
     *
     * @return void
     */
    public function testUpdateTaskThrowsExceptionWhenNotFound(): void
    {
        $this->expectException(NotFoundHttpException::class);

        // The first call of mocked 'find' method will return a task.
        $this->taskRepository->updateTask([
            'id' => 1
        ]);

        // The second call of mocked 'find' method will return null
        // and then thows the not found exception.
        $this->taskRepository->updateTask([
            'id' => 2
        ]);
    }

    /**
     * testDeleteTaskThrowsExceptionWhenNotFound
     *
     * @return void
     */
    public function testDeleteTaskThrowsExceptionWhenNotFound(): void
    {
        $this->expectException(NotFoundHttpException::class);

        // The first call of mocked 'find' method will return a task.
        $this->taskRepository->deleteTask(1);

        // The second call of mocked 'find' method will return null
        // and then thows the not found exception.
        $this->taskRepository->deleteTask(2);
    }
}
