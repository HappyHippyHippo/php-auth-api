<?php

namespace App\Repository\Local;

use App\Model\Entity\Local\Role;
use App\Model\Entity\Local\RolePermission;
use App\Model\Entity\Local\RolePermissionCollection;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Doctrine\Persistence\ManagerRegistry;
use Hippy\Api\Repository\ListResult;
use LogicException;

/**
 * @method RolePermission|null find($id, $lockMode = null, $lockVersion = null)
 * @method RolePermission|null findOneBy(array $criteria, array $orderBy = null)
 * @method RolePermission[]    findAll()
 * @method RolePermission[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 * @extends ServiceEntityRepository<RolePermission>
 */
class RolePermissionRepository extends ServiceEntityRepository
{
    /** @codeCoverageIgnore */
    public function __construct(
        ManagerRegistry $registry,
        private EntityManagerInterface $entityManager,
    ) {
        parent::__construct($registry, RolePermission::class);
    }

    /**
     * @param Role $role
     * @param string|null $search
     * @param int $start
     * @param int $count
     * @param bool $showDeleted
     * @return ListResult
     * @throws NoResultException
     * @throws NonUniqueResultException
     */
    public function search(
        Role $role,
        string|null $search,
        int $start,
        int $count,
        bool $showDeleted = false
    ): ListResult {
        $builder = $this->createQueryBuilder('p');
        $builder
            ->andWhere('p.role = :role')
            ->setParameter(':role', $role)
            ->setFirstResult($start)
            ->setMaxResults($count);

        if (!empty($search)) {
            $builder
                ->andWhere('p.directory like :search')
                ->setParameter(':search', '%' . $search . '%');
        }

        if (!$showDeleted) {
            $builder->andWhere('p.deletedAt is null');
        }

        $records = $builder->getQuery()->execute();
        $total = $builder
            ->select('count(p.id)')
            ->setFirstResult(0)
            ->setMaxResults(1)
            ->getQuery()
            ->getSingleScalarResult();

        return new ListResult(new RolePermissionCollection($records), (string) $search, $start, $count, $total);
    }

    /**
     * @param Role $role
     * @param int $id
     * @param bool $showDeleted
     * @return RolePermission|null
     * @throws LogicException
     */
    public function findById(Role $role, int $id, bool $showDeleted = false): ?RolePermission
    {
        $params = ['role' => $role, 'id' => $id];
        if (!$showDeleted) {
            $params['deletedAt'] = null;
        }
        return $this->findOneBy($params);
    }

    /**
     * @param Role $role
     * @param string $directory
     * @param bool $showDeleted
     * @return RolePermission|null
     * @throws LogicException
     */
    public function findByDirectory(Role $role, string $directory, bool $showDeleted = false): ?RolePermission
    {
        $params = ['role' => $role, 'directory' => $directory];
        if (!$showDeleted) {
            $params['deletedAt'] = null;
        }
        return $this->findOneBy($params);
    }

    /**
     * @param RolePermission $role
     * @return RolePermission
     */
    public function persist(RolePermission $role): RolePermission
    {
        $this->entityManager->persist($role);
        $this->entityManager->flush();
        return $role;
    }

    /**
     * @param RolePermission $role
     * @return RolePermission
     * @throws LogicException
     */
    public function update(RolePermission $role): RolePermission
    {
        $this->entityManager->flush();
        return $role;
    }
}
