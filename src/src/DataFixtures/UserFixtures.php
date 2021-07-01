<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use App\Entity\User;

class UserFixtures extends Fixture
{
    private $passwordHasher;

    private static array $roles = ["ROLE_USER"];
    private static array $emails = [
        "test@test.com",
        "user@test.com",
        "david@tobon.com"
    ];

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }

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
