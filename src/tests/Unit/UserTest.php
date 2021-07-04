<?php

/**
 * Test class for User
 */

declare(strict_types=1);

namespace App\Tests;

use PHPUnit\Framework\TestCase;
use App\Entity\User;

/**
 * @package App\Tests
 * @author David Tobon <jdtobonf@gmail.com>
 */
final class UserTest extends TestCase
{
    /**
     * testSetAndGetEmail
     *
     * @return void
     */
    public function testSetAndGetEmail(): void
    {
        $user = new User();
        $email = "user@email.com";

        $user->setEmail($email);
        $this->assertSame($email, $user->getEmail());
    }

    /**
     * testGetUserIdentifierShouldReturnEmail
     *
     * @return void
     */
    public function testGetUserIdentifierShouldReturnEmail(): void
    {
        $user = new User();
        $email = "user@email.com";

        $user->setEmail($email);
        $this->assertSame($email, $user->getUserIdentifier());
    }

    /**
     * testSetAndGetPassword
     *
     * @return void
     */
    public function testSetAndGetPassword(): void
    {
        $user = new User();
        $password = "My_user_password";

        $user->setPassword($password);
        $this->assertSame($password, $user->getPassword());
    }

    /**
     * testSetAndGetRoles
     *
     * @return void
     */
    public function testSetAndGetRoles(): void
    {
        $user = new User();
        $roles = [
            "ROLE_USER"
        ];

        $user->setRoles($roles);
        $this->assertSame($roles, $user->getRoles());
    }

    /**
     * testGetRolesWillReturnAtLeastTheDefaultRole
     *
     * @return void
     */
    public function testGetRolesWillReturnAtLeastTheDefaultRole(): void
    {
        $user = new User();
        $roles = [
            "ROLE_USER"
        ];

        $user->setRoles([]);
        $this->assertSame($roles, $user->getRoles());
    }
}
