<?php

/**
 * Task repository Class
 */

namespace App\Repository;

use App\Entity\Task;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
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
     * Class constructor
     *
     * @param ManagerRegistry $registry Manager registry
     *
     */
    public function __construct(
        ManagerRegistry $registry
    ) {
        parent::__construct($registry, Task::class);
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

        $this->assignTaskData($task, $data);

        $this->_em->persist($task);
        $this->_em->flush();

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

        $this->assignTaskData($task, $data);

        $this->_em->persist($task);
        $this->_em->flush();

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

        $this->_em->remove($task);
        $this->_em->flush();

        return $task;
    }

    /**
     * Sets the attributes of a task
     *
     * @param Task  $task The task that will be modified
     * @param array $data The data containing the task attributes
     *
     * @return void
     */
    private function assignTaskData(Task &$task, array $data): void
    {
        if (isset($data['title'])) {
            $task->setTitle($data['title']);
        }
        if (isset($data['description'])) {
            $task->setDescription($data['description']);
        }
        if (isset($data['type'])) {
            $task->setType($data['type']);
        }
        if (isset($data['priority'])) {
            $task->setPriority($data['priority']);
        }
        if (isset($data['assignee'])) {
            $task->setAssignee($data['assignee']);
        }
        if (isset($data['status'])) {
            $task->setStatus($data['status']);
        }
    }
}
