<?php

/**
 * Task Data Fixtures Class
 */

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use App\DataFixtures\UserFixtures;
use App\Entity\Task;

/**
 * @package App\DataFixtures
 * @author David Tobon <jdtobonf@gmail.com>
 */
class TaskFixtures extends Fixture implements DependentFixtureInterface
{
    /**
     * @var array $emails Dummy list of emails
     */
    private static array $emails = [
        "test@test.com",
        "user@test.com",
        "david@tobon.com"
    ];

    /**
     * @var array $statuses Dummy list of statuses
     */
    private static array $statuses = [
        "TO_DO",
        "IN_PROGRESS",
        "TESTING",
        "IN_REVIEW",
        "DONE",
        "BLOCKED"
    ];

    /**
     * Loads the fixtures into the database
     *
     * @param ObjectManager $manager Object manager
     *
     * @return void
     */
    public function load(ObjectManager $manager)
    {
        for ($i = 0; $i < 20; $i++) {
            $number = mt_rand(1, 500);

            $task = new Task();
            $task->setTitle("$number: Task");
            $task->setDescription("$number: Description");
            $task->setType($this->getDummyType($number));
            $task->setPriority($this->getDummyPriority($number));
            $task->setAssignee(self::$emails[mt_rand(0, 2)]);
            $task->setStatus(self::$statuses[mt_rand(0, 5)]);

            $manager->persist($task);
        }

        $manager->flush();
    }

    /**
     * Generates a task type based on a random number
     *
     * @param integer $number Random number
     *
     * @return string
     */
    private function getDummyType(int $number): string
    {
        $type = "";
        if ($number < 100) {
            $type = "Bug";
        } elseif ($number < 250) {
            $type = "User Story";
        } else {
            $type = "Feature";
        }

        return $type;
    }

    /**
     * Generates a task priority based on a random number
     *
     * @param integer $number Random number
     *
     * @return integer
     */
    private function getDummyPriority(int $number): int
    {
        $priority = 0;
        if ($number < 100) {
            $priority = 1;
        } elseif ($number < 250) {
            $priority = 2;
        } else {
            $priority = 3;
        }

        return $priority;
    }

    /**
     * Defines the dependencies for this fixture to run
     *
     * @return array
     */
    public function getDependencies(): array
    {
        return [
            UserFixtures::class,
        ];
    }
}
