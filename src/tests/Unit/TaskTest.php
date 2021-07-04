<?php

/**
 * Test class for Task
 */

declare(strict_types=1);

namespace App\Tests;

use PHPUnit\Framework\TestCase;
use App\Entity\Task;

/**
 * @package App\Tests
 * @author David Tobon <jdtobonf@gmail.com>
 */
final class TaskTest extends TestCase
{
    /**
     * testSetAndGetTitle
     *
     * @return void
     */
    public function testSetAndGetTitle(): void
    {
        $task = new Task();
        $title = "My task title";

        $task->setTitle($title);
        $this->assertSame($title, $task->getTitle());
    }

    /**
     * testSetAndGetDescription
     *
     * @return void
     */
    public function testSetAndGetDescription(): void
    {
        $task = new Task();
        $description = "My task description";

        $task->setDescription($description);
        $this->assertSame($description, $task->getDescription());
    }

    /**
     * testSetAndGetType
     *
     * @return void
     */
    public function testSetAndGetType(): void
    {
        $task = new Task();
        $type = "My task type";

        $task->setType($type);
        $this->assertSame($type, $task->getType());
    }

    /**
     * testSetAndGetPriority
     *
     * @return void
     */
    public function testSetAndGetPriority(): void
    {
        $task = new Task();
        $priority = 2;

        $task->setPriority($priority);
        $this->assertSame($priority, $task->getPriority());
    }

    /**
     * testSetAndGetAssignee
     *
     * @return void
     */
    public function testSetAndGetAssignee(): void
    {
        $task = new Task();
        $assignee = "test@email.com";

        $task->setAssignee($assignee);
        $this->assertSame($assignee, $task->getAssignee());
    }

    /**
     * testSetAndGetStatus
     *
     * @return void
     */
    public function testSetAndGetStatus(): void
    {
        $task = new Task();
        $status = "DONE";

        $task->setStatus($status);
        $this->assertSame($status, $task->getStatus());
    }
}
