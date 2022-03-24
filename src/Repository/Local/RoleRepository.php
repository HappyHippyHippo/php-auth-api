<?php

namespace App\Repository\Local;

use App\Model\Entity\Local\Role;
use App\Model\Entity\Local\RoleCollection;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Doctrine\Persistence\ManagerRegistry;
use Hippy\Api\Repository\ListResult;
use LogicException;

/**
 * @method Role|null find($id, $lockMode = null, $lockVersion = null)
 * @method Role|null findOneBy(array $criteria, array $orderBy = null)
 * @method Role[]    findAll()
 * @method Role[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 * @extends ServiceEntityRepository<Role>
 */
class RoleRepository extends ServiceEntityRepository
{
    /** @codeCoverageIgnore */
    public function __construct(
        ManagerRegistry $registry,
        private EntityManagerInterface $entityManager,
    ) {
        parent::__construct($registry, Role::class);
    }

    /**
     * @param int[]|string|null $search
     * @param int $start
     * @param int $count
     * @param bool $showDeleted
     * @return ListResult
     * @throws NoResultException
     * @throws NonUniqueResultException
     */
    public function search(
        array|string|null $search,
        int $start,
        int $count,
        bool $showDeleted = false
    ): ListResult {
        if (is_null($search) || is_string($search)) {
            return $this->searchByTerm($search, $start, $count, $showDeleted);
        }

        return $this->searchByIds($search, $start, $count, $showDeleted);
    }

    /**
     * @param string|null $search
     * @param int $start
     * @param int $count
     * @param bool $showDeleted
     * @return ListResult
     * @throws NoResultException
     * @throws NonUniqueResultException
     */
    protected function searchByTerm(
        string|null $search,
        int $start,
        int $count,
        bool $showDeleted = false
    ): ListResult {
        $builder = $this->createQueryBuilder('r');
        $builder->setFirstResult($start)->setMaxResults($count);

        $whereAction = 'where';
        if (!empty($search)) {
            $builder
                ->$whereAction('r.name like :search')
                ->setParameter(':search', '%' . $search . '%');
            $whereAction = 'andWhere';
        }

        if (!$showDeleted) {
            $builder->$whereAction('r.deletedAt is null');
        }

        $records = $builder->getQuery()->execute();
        $total = $builder
            ->select('count(r.id)')
            ->setFirstResult(0)
            ->setMaxResults(1)
            ->getQuery()
            ->getSingleScalarResult();

        return new ListResult(new RoleCollection($records), (string) $search, $start, $count, $total);
    }

    /**
     * @param int[] $ids
     * @param int $start
     * @param int $count
     * @param bool $showDeleted
     * @return ListResult
     * @throws NoResultException
     * @throws NonUniqueResultException
     */
    protected function searchByIds(
        array $ids,
        int $start,
        int $count,
        bool $showDeleted = false
    ): ListResult {
        $ids = array_map(function (string|int $id): int {
            return (int) $id;
        }, $ids);

        $builder = $this->createQueryBuilder('r');
        $builder
            ->where('r.id in (:search)')
            ->setParameter(':search', $ids)
            ->setFirstResult($start)
            ->setMaxResults($count);

        if (!$showDeleted) {
            $builder->andWhere('r.deletedAt is null');
        }

        $records = $builder->getQuery()->execute();
        $total = $builder
            ->select('count(r.id)')
            ->setFirstResult(0)
            ->setMaxResults(1)
            ->getQuery()
            ->getSingleScalarResult();

        return new ListResult(new RoleCollection($records), (string) json_encode($ids), $start, $count, $total);
    }

    /**
     * @param int $id
     * @param bool $showDeleted
     * @return Role|null
     * @throws LogicException
     */
    public function findById(int $id, bool $showDeleted = false): ?Role
    {
        $params = ['id' => $id];
        if (!$showDeleted) {
            $params['deletedAt'] = null;
        }
        return $this->findOneBy($params);
    }

    /**
     * @param string $name
     * @param bool $showDeleted
     * @return Role|null
     * @throws LogicException
     */
    public function findByName(string $name, bool $showDeleted = false): ?Role
    {
        $params = ['name' => $name];
        if (!$showDeleted) {
            $params['deletedAt'] = null;
        }
        return $this->findOneBy($params);
    }

    /**
     * @param Role $role
     * @return Role
     */
    public function persist(Role $role): Role
    {
        $this->entityManager->persist($role);
        $this->entityManager->flush();
        return $role;
    }

    /**
     * @param Role $role
     * @return Role
     * @throws LogicException
     */
    public function update(Role $role): Role
    {
        $this->entityManager->flush();
        return $role;
    }
}
