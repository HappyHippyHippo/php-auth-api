<?php

namespace App\Tests;

use App\Model\Entity\Local\Challenge;
use App\Model\Entity\Local\PermissionLevel;
use App\Model\Entity\Local\Role;
use App\Model\Entity\Local\RolePermission;
use App\Model\Entity\Local\Token;
use App\Model\Entity\Local\User;
use App\Model\Entity\Local\UserPermission;
use App\Model\Entity\Local\UserRole;
use DateTime;
use Exception;

class LocalFixtures extends Fixtures
{
    /**
     * @param array<string, mixed> $data
     * @return User
     * @throws Exception
     */
    public function addUser(array $data = []): User
    {
        // @phpstan-ignore-next-line
        return $this->add(new User(array_merge([
            'enabled' => true,
            'email' => 'email@email.com',
            'password' => hash('sha512', '__dummy_password__'),
            'salt' => hash('sha512', '__dummy_password__'),
            'tries' => 3,
            'coolDown' => new DateTime('1900-01-01 00:00:00'),
            'createdAt' => new DateTime('2000-01-01 00:00:00'),
            'createdBy' => null,
            'updatedAt' => new DateTime('2000-01-01 00:00:00'),
            'updatedBy' => null,
            'deletedAt' => null,
            'deletedBy' => null,
        ], $data)));
    }

    /**
     * @param array<string, mixed> $data
     * @return Challenge
     * @throws Exception
     */
    public function addChallenge(array $data = []): Challenge
    {
        // @phpstan-ignore-next-line
        return $this->add(new Challenge(array_merge([
            'challenge' => hash('sha512', '__dummy_challenge__'),
            'salt' => hash('sha512', '__dummy_salt__'),
            'message' => null,
            'ttl' => new DateTime(date('Y-m-d H:i:s', strtotime('+1 day'))),
            'createdAt' => new DateTime('2000-01-01 00:00:00'),
            'createdBy' => null,
            'updatedAt' => new DateTime('2000-01-01 00:00:00'),
            'updatedBy' => null,
            'deletedAt' => null,
            'deletedBy' => null,
        ], $data)));
    }

    /**
     * @param array<string, mixed> $data
     * @return Token
     * @throws Exception
     */
    public function addToken(array $data = []): Token
    {
        // @phpstan-ignore-next-line
        return $this->add(new Token(array_merge([
            'jwt' => '__dummy_jwt__',
            'secret' => '__dummy_secret__',
            'recover' => '__dummy_recover__',
            'ttl' => new DateTime(date('Y-m-d H:i:s', strtotime('+1 day'))),
            'createdAt' => new DateTime('2000-01-01 00:00:00'),
            'createdBy' => null,
            'updatedAt' => new DateTime('2000-01-01 00:00:00'),
            'updatedBy' => null,
            'deletedAt' => null,
            'deletedBy' => null,
        ], $data)));
    }

    /**
     * @param array<string, mixed> $data
     * @return Role
     * @throws Exception
     */
    public function addRole(array $data = []): Role
    {
        // @phpstan-ignore-next-line
        return $this->add(new Role(array_merge([
            'enabled' => true,
            'name' => '__dummy_name__',
            'description' => '__dummy_description__',
            'priority' => 0,
            'createdAt' => new DateTime('2000-01-01 00:00:00'),
            'createdBy' => null,
            'updatedAt' => new DateTime('2000-01-01 00:00:00'),
            'updatedBy' => null,
            'deletedAt' => null,
            'deletedBy' => null,
        ], $data)));
    }

    /**
     * @param array<string, mixed> $data
     * @return RolePermission
     * @throws Exception
     */
    public function addRolePermission(array $data = []): RolePermission
    {
        // @phpstan-ignore-next-line
        return $this->add(new RolePermission(array_merge([
            'enabled' => true,
            'directory' => '__dummy_directory__',
            'description' => '__dummy_description__',
            'level' => PermissionLevel::NONE,
            'createdAt' => new DateTime('2000-01-01 00:00:00'),
            'createdBy' => null,
            'updatedAt' => new DateTime('2000-01-01 00:00:00'),
            'updatedBy' => null,
            'deletedAt' => null,
            'deletedBy' => null,
        ], $data)));
    }

    /**
     * @param array<string, mixed> $data
     * @return UserPermission
     * @throws Exception
     */
    public function addUserPermission(array $data = []): UserPermission
    {
        // @phpstan-ignore-next-line
        return $this->add(new UserPermission(array_merge([
            'enabled' => true,
            'directory' => '__dummy_directory__',
            'description' => '__dummy_description__',
            'level' => PermissionLevel::NONE,
            'createdAt' => new DateTime('2000-01-01 00:00:00'),
            'createdBy' => null,
            'updatedAt' => new DateTime('2000-01-01 00:00:00'),
            'updatedBy' => null,
            'deletedAt' => null,
            'deletedBy' => null,
        ], $data)));
    }

    /**
     * @param array<string, mixed> $data
     * @return UserRole
     * @throws Exception
     */
    public function addUserRole(array $data = []): UserRole
    {
        // @phpstan-ignore-next-line
        return $this->add(new UserRole(array_merge([
            'enabled' => true,
            'priority' => 0,
            'createdAt' => new DateTime('2000-01-01 00:00:00'),
            'createdBy' => null,
            'updatedAt' => new DateTime('2000-01-01 00:00:00'),
            'updatedBy' => null,
            'deletedAt' => null,
            'deletedBy' => null,
        ], $data)));
    }
}
