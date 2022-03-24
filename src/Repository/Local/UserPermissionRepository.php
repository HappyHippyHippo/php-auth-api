<?php

namespace App\Repository\Local;

use App\Model\Entity\Local\User;
use App\Model\Entity\Local\UserPermission;
use App\Model\Entity\Local\UserPermissionCollection;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Doctrine\Persistence\ManagerRegistry;
use Hippy\Api\Repository\ListResult;
use LogicException;

/**
 * @method UserPermission|null find($id, $lockMode = null, $lockVersion = null)
 * @method UserPermission|null findOneBy(array $criteria, array $orderBy = null)
 * @method UserPermission[]    findAll()
 * @method UserPermission[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 * @extends ServiceEntityRepository<UserPermission>
 */
class UserPermissionRepository extends ServiceEntityRepository
{
    /** @codeCoverageIgnore */
    public function __construct(
        ManagerRegistry $registry,
        private EntityManagerInterface $entityManager,
    ) {
        parent::__construct($registry, UserPermission::class);
    }

    /**
     * @param User $user
     * @param string|null $search
     * @param int $start
     * @param int $count
     * @param bool $showDeleted
     * @return ListResult
     * @throws NoResultException
     * @throws NonUniqueResultException
     */
    public function search(
        User $user,
        string|null $search,
        int $start,
        int $count,
        bool $showDeleted = false
    ): ListResult {
        $builder = $this->createQueryBuilder('p');
        $builder
            ->where('p.user = :user')
            ->setParameter(':user', $user)
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

        $records = $builder->getQuery() ->execute();
        $total = $builder
            ->select('count(p.id)')
            ->setFirstResult(0)
            ->setMaxResults(1)
            ->getQuery()
            ->getSingleScalarResult();

        return new ListResult(new UserPermissionCollection($records), (string) $search, $start, $count, $total);
    }

    /**
     * @param User $user
     * @param int $id
     * @param bool $showDeleted
     * @return UserPermission|null
     * @throws LogicException
     */
    public function findById(User $user, int $id, bool $showDeleted = false): ?UserPermission
    {
        $params = ['user' => $user, 'id' => $id];
        if (!$showDeleted) {
            $params['deletedAt'] = null;
        }
        return $this->findOneBy($params);
    }

    /**
     * @param User $user
     * @param string $directory
     * @param bool $showDeleted
     * @return UserPermission|null
     * @throws LogicException
     */
    public function findByDirectory(User $user, string $directory, bool $showDeleted = false): ?UserPermission
    {
        $params = ['user' => $user, 'directory' => $directory];
        if (!$showDeleted) {
            $params['deletedAt'] = null;
        }
        return $this->findOneBy($params);
    }

    /**
     * @param UserPermission $user
     * @return UserPermission
     */
    public function persist(UserPermission $user): UserPermission
    {
        $this->entityManager->persist($user);
        $this->entityManager->flush();
        return $user;
    }

    /**
     * @param UserPermission $user
     * @return UserPermission
     * @throws LogicException
     */
    public function update(UserPermission $user): UserPermission
    {
        $this->entityManager->flush();
        return $user;
    }
}
