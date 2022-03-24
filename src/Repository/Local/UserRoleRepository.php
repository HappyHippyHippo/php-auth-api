<?php

namespace App\Repository\Local;

use App\Model\Entity\Local\Role;
use App\Model\Entity\Local\User;
use App\Model\Entity\Local\UserRole;
use App\Model\Entity\Local\UserRoleCollection;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use LogicException;

/**
 * @method UserRole|null find($id, $lockMode = null, $lockVersion = null)
 * @method UserRole|null findOneBy(array $criteria, array $orderBy = null)
 * @method UserRole[]    findAll()
 * @method UserRole[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 * @extends ServiceEntityRepository<UserRole>
 */
class UserRoleRepository extends ServiceEntityRepository
{
    /** @codeCoverageIgnore */
    public function __construct(
        ManagerRegistry $registry,
        private EntityManagerInterface $entityManager,
    ) {
        parent::__construct($registry, UserRole::class);
    }

    /**
     * @param User $user
     * @param bool $showDeleted
     * @return UserRoleCollection
     */
    public function searchByUser(User $user, bool $showDeleted = false): UserRoleCollection
    {
        $params = ['user' => $user];
        if (!$showDeleted) {
            $params['deletedAt'] = null;
        }
        return new UserRoleCollection($this->findBy($params));
    }

    /**
     * @param User $user
     * @param Role $role
     * @param bool $showDeleted
     * @return ?UserRole
     */
    public function findByUserAndRole(User $user, Role $role, bool $showDeleted = false): ?UserRole
    {
        $params = ['user' => $user, 'role' => $role];
        if (!$showDeleted) {
            $params['deletedAt'] = null;
        }
        return $this->findOneBy($params);
    }

    /**
     * @param User $user
     * @param int $priority
     * @return void
     */
    public function shiftPriority(User $user, int $priority): void
    {
        $this->entityManager
            ->createQuery(
                'UPDATE Local:UserRole ur
                SET ur.priority = ur.priority + 1
                WHERE ur.user = :user
                AND ur.priority >= :priority'
            )->setParameter(':user', $user)
            ->setParameter(':priority', $priority)
            ->execute();
    }

    /**
     * @param UserRole $user
     * @return UserRole
     */
    public function persist(UserRole $user): UserRole
    {
        $this->entityManager->persist($user);
        $this->entityManager->flush();
        return $user;
    }

    /**
     * @param UserRole $user
     * @return UserRole
     * @throws LogicException
     */
    public function update(UserRole $user): UserRole
    {
        $this->entityManager->flush();
        return $user;
    }
}
