<?php

namespace App\Repository\Local;

use App\Model\Entity\Local\User;
use App\Model\Entity\Local\UserCollection;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Doctrine\Persistence\ManagerRegistry;
use Hippy\Api\Repository\ListResult;
use LogicException;

/**
 * @method User|null find($id, $lockMode = null, $lockVersion = null)
 * @method User|null findOneBy(array $criteria, array $orderBy = null)
 * @method User[]    findAll()
 * @method User[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 * @extends ServiceEntityRepository<User>
 */
class UserRepository extends ServiceEntityRepository
{
    /** @codeCoverageIgnore */
    public function __construct(
        ManagerRegistry $registry,
        private EntityManagerInterface $entityManager,
    ) {
        parent::__construct($registry, User::class);
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
        $builder = $this->createQueryBuilder('u');
        $builder
            ->setFirstResult($start)
            ->setMaxResults($count);

        $whereAction = 'where';
        if (!empty($search)) {
            $builder
                ->$whereAction('u.email like :search')
                ->setParameter(':search', '%' . $search . '%');
            $whereAction = 'andWhere';
        }

        if (!$showDeleted) {
            $builder->$whereAction('u.deletedAt is null');
        }

        $records = $builder->getQuery()->execute();
        $total = $builder
            ->select('count(u.id)')
            ->setFirstResult(0)
            ->setMaxResults(1)
            ->getQuery()
            ->getSingleScalarResult();

        return new ListResult(new UserCollection($records), (string) $search, $start, $count, $total);
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

        $builder = $this->createQueryBuilder('u');
        $builder
            ->where('u.id in (:search)')
            ->setParameter(':search', $ids)
            ->setFirstResult($start)
            ->setMaxResults($count);

        if (!$showDeleted) {
            $builder->andWhere('u.deletedAt is null');
        }

        $records = $builder->getQuery()->execute();
        $total = $builder
            ->select('count(u.id)')
            ->setFirstResult(0)
            ->setMaxResults(1)
            ->getQuery()
            ->getSingleScalarResult();

        return new ListResult(new UserCollection($records), (string) json_encode($ids), $start, $count, $total);
    }

    /**
     * @param int $id
     * @param bool $showDeleted
     * @return User|null
     * @throws LogicException
     */
    public function findById(int $id, bool $showDeleted = false): ?User
    {
        $params = ['id' => $id];
        if (!$showDeleted) {
            $params['deletedAt'] = null;
        }
        return $this->findOneBy($params);
    }

    /**
     * @param string $email
     * @param bool $showDeleted
     * @return User|null
     */
    public function findByEmail(string $email, bool $showDeleted = false): ?User
    {
        $params = ['email' => $email];
        if (!$showDeleted) {
            $params['deletedAt'] = null;
        }
        return $this->findOneBy($params);
    }

    /**
     * @param User $user
     * @return User
     */
    public function persist(User $user): User
    {
        $this->entityManager->persist($user);
        $this->entityManager->flush();
        return $user;
    }

    /**
     * @param User $user
     * @return User
     * @throws LogicException
     */
    public function update(User $user): User
    {
        $this->entityManager->flush();
        return $user;
    }
}
