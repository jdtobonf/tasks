<?php

/**
 * User Data Fixtures Class
 */

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use App\Entity\User;

/**
 * @package App\DataFixtures
 * @author David Tobon <jdtobonf@gmail.com>
 */
class UserFixtures extends Fixture
{
    /**
     * @var UserPasswordHasherInterface $passwordHasher Password hasher
     */
    private UserPasswordHasherInterface $passwordHasher;

    /**
     * @var array $roles User roles
     */
    private static array $roles = ["ROLE_USER"];

    /**
     * @var array $emails Dummy list of emails
     */
    private static array $emails = [
        "test@test.com",
        "user@test.com",
        "david@tobon.com"
    ];

    /**
     * UserFixtures constructor
     *
     * @param UserPasswordHasherInterface $passwordHasher Password hasher
     */
    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }

    /**
     * Loads the fixtures into the database
     *
     * @param ObjectManager $manager Object manager
     *
     * @return void
     */
    public function load(ObjectManager $manager)
    {
        foreach (self::$emails as $email) {
            $user = new User();
            $user->setEmail($email);
            $user->setRoles(self::$roles);

            $user->setPassword($this->passwordHasher->hashPassword(
                $user,
                'secret'
            ));

            $manager->persist($user);
        }

        $manager->flush();
    }
}
