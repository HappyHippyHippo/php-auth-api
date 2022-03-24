<?php

namespace App\Repository\Local;

use App\Model\Entity\Local\Challenge;
use App\Model\Entity\Local\User;
use DateTime;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Challenge|null find($id, $lockMode = null, $lockVersion = null)
 * @method Challenge|null findOneBy(array $criteria, array $orderBy = null)
 * @method Challenge[]    findAll()
 * @method Challenge[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 * @extends ServiceEntityRepository<Challenge>
 */
class ChallengeRepository extends ServiceEntityRepository
{
    /**
     * @param ManagerRegistry $registry
     * @param EntityManagerInterface $entityManager
     * @codeCoverageIgnore
     */
    public function __construct(
        ManagerRegistry $registry,
        private EntityManagerInterface $entityManager
    ) {
        parent::__construct($registry, Challenge::class);
    }

    /**
     * @param User $user
     * @param bool $showDeleted
     * @return Challenge|null
     * @throws NonUniqueResultException
     */
    public function findOfUser(User $user, bool $showDeleted = false): ?Challenge
    {
        $builder = $this->createQueryBuilder('c')
            ->where('c.ttl > :ttl')
            ->andWhere('c.user = :user')
            ->setParameter('ttl', new DateTime())
            ->setParameter('user', $user)
            ->setMaxResults(1);

        if (!$showDeleted) {
            $builder->andWhere('c.deletedAt is null');
        }

        return $builder->getQuery()->getOneOrNullResult();
    }

    /**
     * @param Challenge $challenge
     * @return Challenge
     */
    public function persist(Challenge $challenge): Challenge
    {
        $this->entityManager->persist($challenge);
        $this->entityManager->flush();
        return $challenge;
    }

    /**
     * @param Challenge $challenge
     * @return Challenge
     */
    public function update(Challenge $challenge): Challenge
    {
        $this->entityManager->flush();
        return $challenge;
    }
}
