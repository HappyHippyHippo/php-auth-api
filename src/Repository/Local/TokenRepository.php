<?php

namespace App\Repository\Local;

use App\Model\Entity\Local\Token;
use App\Model\Entity\Local\User;
use DateTime;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Token|null find($id, $lockMode = null, $lockVersion = null)
 * @method Token|null findOneBy(array $criteria, array $orderBy = null)
 * @method Token[]    findAll()
 * @method Token[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 * @extends ServiceEntityRepository<Token>
 */
class TokenRepository extends ServiceEntityRepository
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
        parent::__construct($registry, Token::class);
    }

    /**
     * @param string $token
     * @param bool $showDeleted
     * @return Token|null
     */
    public function findByJwt(string $token, bool $showDeleted = false): ?Token
    {
        $params = ['jwt' => $token];
        if (!$showDeleted) {
            $params['deletedAt'] = null;
        }
        return $this->findOneBy($params);
    }

    /**
     * @param User $user
     * @param bool $showDeleted
     * @return Token|null
     * @throws NonUniqueResultException
     */
    public function findActiveOfUser(User $user, bool $showDeleted = false): ?Token
    {
        $builder = $this->createQueryBuilder('t')
            ->where('t.ttl > :ttl')
            ->andWhere('t.user = :user')
            ->setParameter('ttl', new DateTime())
            ->setParameter('user', $user)
            ->setMaxResults(1);

        if (!$showDeleted) {
            $builder->andWhere('t.deletedAt is null');
        }

        return $builder->getQuery()->getOneOrNullResult();
    }

    /**
     * @param User $user
     * @param bool $showDeleted
     * @return Token|null
     * @throws NonUniqueResultException
     */
    public function findLastOfUser(User $user, bool $showDeleted = false): ?Token
    {
        $builder = $this->createQueryBuilder('t')
            ->where('t.user = :user')
            ->orderBy('t.createdAt', 'DESC')
            ->setParameter('user', $user)
            ->setMaxResults(1);

        if (!$showDeleted) {
            $builder->andWhere('t.deletedAt is null');
        }

        return $builder->getQuery()->getOneOrNullResult();
    }

    /**
     * @param Token $token
     * @return Token
     */
    public function persist(Token $token): Token
    {
        $this->entityManager->persist($token);
        $this->entityManager->flush();
        return $token;
    }
}
