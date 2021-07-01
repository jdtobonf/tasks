<?php

/**
 * Task repository Class
 */

namespace App\Repository;

use App\Entity\Task;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * @package App\Repository
 * @author David Tobon <jdtobonf@gmail.com>
 *
 * @method Task|null find($id, $lockMode = null, $lockVersion = null)
 * @method Task|null findOneBy(array $criteria, array $orderBy = null)
 * @method Task[]    findAll()
 * @method Task[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TaskRepository extends ServiceEntityRepository
{
    /**
     * @var EntityManagerInterface $manager
     * Manager that performs operations in the database
     */
    private EntityManagerInterface $manager;

    /**
     * Class constructor
     *
     * @param ManagerRegistry $registry Manager registry
     *
     * @param EntityManagerInterface $entityManager Manager to perform operations in DB
     *
     */
    public function __construct(
        ManagerRegistry $registry,
        EntityManagerInterface $entityManager
    ) {
        parent::__construct($registry, Task::class);
        $this->manager = $entityManager;
    }

    /**
     * Get the tasks that are assigned to the logged in user
     *
     * @param string $email The email of the logged in user
     *
     * @return array
     */
    public function getUserTasks(string $email): array
    {
        return $this->findBy([
            'assignee' => $email
        ]);
    }

    /**
     * Creates a task based on the data it gets
     *
     * @param array $data The data to save
     *
     * @return Task
     */
    public function createTask(array $data): Task
    {
        $task = new Task();

        // TODO validations
        $task->setTitle($data['title']);
        $task->setDescription($data['description']);
        $task->setType($data['type']);
        $task->setPriority($data['priority']);
        $task->setAssignee($data['assignee']);
        $task->setStatus($data['status']);

        $this->manager->persist($task);
        $this->manager->flush();

        return $task;
    }

    /**
     * Updates a task based on the data it gets
     *
     * @param array $data Task data to update
     *
     * @return Task
     */
    public function updateTask(array $data): Task
    {
        $task = $this->find($data['id']);

        if (empty($task)) {
            throw new NotFoundHttpException("Task ${data['id']} was not found");
        }

        // TODO validations
        $task->setTitle($data['title']);
        $task->setDescription($data['description']);
        $task->setType($data['type']);
        $task->setPriority($data['priority']);
        $task->setAssignee($data['assignee']);
        $task->setStatus($data['status']);

        $this->manager->persist($task);
        $this->manager->flush();

        return $task;
    }

    /**
     * Deletes a task by id
     *
     * @param integer $id The id of the task
     *
     * @return Task
     */
    public function deleteTask(int $id): Task
    {
        $task = $this->find($id);

        if (empty($task)) {
            throw new NotFoundHttpException("Task $id was not found");
        }

        $this->manager->remove($task);
        $this->manager->flush();

        return $task;
    }
}
