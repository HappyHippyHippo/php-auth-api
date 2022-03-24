<?php

namespace App\Repository;

use App\Repository\Local\ChallengeRepository;
use App\Repository\Local\RolePermissionRepository;
use App\Repository\Local\RoleRepository;
use App\Repository\Local\TokenRepository;
use App\Repository\Local\UserPermissionRepository;
use App\Repository\Local\UserRepository;
use App\Repository\Local\UserRoleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Hippy\Repository\RepositoryFactoryInterface;

class RepositoryFactory implements RepositoryFactoryInterface
{
    /** @var string */
    protected const REPO_LOCAL_USER = 'local.user';

    /** @var string */
    protected const REPO_LOCAL_USER_PERMISSION = 'local.user.permission';

    /** @var string */
    protected const REPO_LOCAL_USER_ROLE = 'local.user.roles';

    /** @var string */
    protected const REPO_LOCAL_TOKEN = 'local.token';

    /** @var string */
    protected const REPO_LOCAL_CHALLENGE = 'local.challenge';

    /** @var string */
    protected const REPO_LOCAL_ROLE = 'local.role';

    /** @var string */
    protected const REPO_LOCAL_ROLE_PERMISSION = 'local.role.permission';

    /** @var array<string, mixed> */
    protected array $repos;

    /**
     * @param ManagerRegistry $registry
     * @param EntityManagerInterface $localEntityManager
     */
    public function __construct(
        private ManagerRegistry $registry,
        private EntityManagerInterface $localEntityManager,
    ) {
    }

    /**
     * @return UserRepository
     */
    public function getUsers(): UserRepository
    {
        if (!isset($this->repos[self::REPO_LOCAL_USER])) {
            $this->repos[self::REPO_LOCAL_USER] = $this->createRepo(
                UserRepository::class,
                $this->registry,
                $this->localEntityManager,
            );
        }
        return $this->repos[self::REPO_LOCAL_USER];
    }

    /**
     * @return UserPermissionRepository
     */
    public function getUserPermissions(): UserPermissionRepository
    {
        if (!isset($this->repos[self::REPO_LOCAL_USER_PERMISSION])) {
            $this->repos[self::REPO_LOCAL_USER_PERMISSION] = $this->createRepo(
                UserPermissionRepository::class,
                $this->registry,
                $this->localEntityManager
            );
        }
        return $this->repos[self::REPO_LOCAL_USER_PERMISSION];
    }

    /**
     * @return UserRoleRepository
     */
    public function getUserRoles(): UserRoleRepository
    {
        if (!isset($this->repos[self::REPO_LOCAL_USER_ROLE])) {
            $this->repos[self::REPO_LOCAL_USER_ROLE] = $this->createRepo(
                UserRoleRepository::class,
                $this->registry,
                $this->localEntityManager
            );
        }
        return $this->repos[self::REPO_LOCAL_USER_ROLE];
    }

    /**
     * @return TokenRepository
     */
    public function getTokens(): TokenRepository
    {
        if (!isset($this->repos[self::REPO_LOCAL_TOKEN])) {
            $this->repos[self::REPO_LOCAL_TOKEN] = $this->createRepo(
                TokenRepository::class,
                $this->registry,
                $this->localEntityManager
            );
        }
        return $this->repos[self::REPO_LOCAL_TOKEN];
    }

    /**
     * @return ChallengeRepository
     */
    public function getChallenges(): ChallengeRepository
    {
        if (!isset($this->repos[self::REPO_LOCAL_CHALLENGE])) {
            $this->repos[self::REPO_LOCAL_CHALLENGE] = $this->createRepo(
                ChallengeRepository::class,
                $this->registry,
                $this->localEntityManager
            );
        }
        return $this->repos[self::REPO_LOCAL_CHALLENGE];
    }

    /**
     * @return RoleRepository
     */
    public function getRoles(): RoleRepository
    {
        if (!isset($this->repos[self::REPO_LOCAL_ROLE])) {
            $this->repos[self::REPO_LOCAL_ROLE] = $this->createRepo(
                RoleRepository::class,
                $this->registry,
                $this->localEntityManager
            );
        }
        return $this->repos[self::REPO_LOCAL_ROLE];
    }

    /**
     * @return RolePermissionRepository
     */
    public function getRolePermissions(): RolePermissionRepository
    {
        if (!isset($this->repos[self::REPO_LOCAL_ROLE_PERMISSION])) {
            $this->repos[self::REPO_LOCAL_ROLE_PERMISSION] = $this->createRepo(
                RolePermissionRepository::class,
                $this->registry,
                $this->localEntityManager
            );
        }
        return $this->repos[self::REPO_LOCAL_ROLE_PERMISSION];
    }

    /**
     * @param string $class
     * @param mixed ...$args
     * @return mixed
     * @codeCoverageIgnore
     */
    protected function createRepo(string $class, mixed ...$args): mixed
    {
        return new $class(...$args);
    }
}
