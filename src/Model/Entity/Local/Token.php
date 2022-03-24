<?php

namespace App\Model\Entity\Local;

use App\Repository\Local\TokenRepository;
use DateTime;
use Doctrine\ORM\Mapping as ORM;

/**
 * @method int|null getId()
 * @method Token setId(int|null $value)
 * @method User|null getUser()
 * @method Token setUser(User|null $value)
 * @method string|null getJwt()
 * @method Token setJwt(string|null $value)
 * @method string|null getSecret()
 * @method Token setSecret(string|null $value)
 * @method string|null getRecover()
 * @method Token setRecover(string|null $value)
 * @method DateTime|null getTtl()
 * @method Token setTtl(DateTime|null $value)
 * @method DateTime|null getCreatedAt()
 * @method Token setCreatedAt(DateTime|null $value)
 * @method User|null getCreatedBy()
 * @method Token setCreatedBy(User|null $value)
 * @method DateTime|null getUpdatedAt()
 * @method Token setUpdatedAt(DateTime|null $value)
 * @method User|null getUpdatedBy()
 * @method Token setUpdatedBy(User|null $value)
 * @method DateTime|null getDeletedAt()
 * @method Token setDeletedAt(DateTime|null $value)
 * @method User|null getDeletedBy()
 * @method Token setDeletedBy(User|null $value)
 *
 * @ORM\Entity(repositoryClass=TokenRepository::class)
 * @ORM\Table(
 *      name="token",
 *      indexes={
 *          @ORM\Index(name="idx_token_user", columns={"user_id"})
 *      },
 *      uniqueConstraints={
 *          @ORM\Index(name="idx_token_jwt", columns={"jwt"})
 *      })
 */
class Token extends Entity
{
    /**
     * @var User|null
     * @ORM\ManyToOne(targetEntity="User", cascade={"persist"})
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id", nullable=false)
     */
    protected ?User $user;

    /**
     * @var string|null
     * @ORM\Column(type="text", nullable=false)
     */
    protected ?string $jwt;

    /**
     * @var string|null
     * @ORM\Column(type="string", length=64, nullable=false, options={"fixed"=true})
     */
    protected ?string $secret;

    /**
     * @var string|null
     * @ORM\Column(type="string", length=64, nullable=false, options={"fixed"=true})
     */
    protected ?string $recover;

    /**
     * @var DateTime|null
     * @ORM\Column(type="datetime", nullable=false)
     */
    protected ?DateTime $ttl;
}
