<?php

namespace App\Model\Entity\Local;

use App\Repository\Local\ChallengeRepository;
use DateTime;
use Doctrine\ORM\Mapping as ORM;

/**
 * @method int|null getId()
 * @method Challenge setId(int|null $value)
 * @method User|null getUser()
 * @method Challenge setUser(User|null $value)
 * @method Token|null getToken()
 * @method string|null getChallenge()
 * @method Challenge setChallenge(string|null $value)
 * @method string|null getSalt()
 * @method Challenge setSalt(string|null $value)
 * @method string|null getMessage()
 * @method Challenge setMessage(string|null $value)
 * @method DateTime|null getTtl()
 * @method Challenge setTtl(DateTime|null $value)
 * @method DateTime|null getCreatedAt()
 * @method Challenge setCreatedAt(DateTime|null $value)
 * @method User|null getCreatedBy()
 * @method Challenge setCreatedBy(User|null $value)
 * @method DateTime|null getUpdatedAt()
 * @method Challenge setUpdatedAt(DateTime|null $value)
 * @method User|null getUpdatedBy()
 * @method Challenge setUpdatedBy(User|null $value)
 * @method DateTime|null getDeletedAt()
 * @method Challenge setDeletedAt(DateTime|null $value)
 * @method User|null getDeletedBy()
 * @method Challenge setDeletedBy(User|null $value)
 *
 * @ORM\Entity(repositoryClass=ChallengeRepository::class)
 * @ORM\Table(
 *      name="challenge",
 *      indexes={
 *          @ORM\Index(name="idx_challenge_user", columns={"user_id"}),
 *          @ORM\Index(name="idx_challenge_token", columns={"token_id"}),
 *          @ORM\Index(name="idx_challenge_challenge", columns={"challenge"})
 *      })
 */
class Challenge extends Entity
{
    /**
     * @var User|null
     * @ORM\ManyToOne(targetEntity="User", cascade={"persist"})
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id", nullable=false)
     */
    protected ?User $user;

    /**
     * @var Token|null
     * @ORM\OneToOne(targetEntity="Token", cascade={"persist"})
     * @ORM\JoinColumn(name="token_id", referencedColumnName="id")
     */
    protected ?Token $token;

    /**
     * @var string|null
     * @ORM\Column(type="string", length=64, options={"fixed"=true}, nullable=true)
     */
    protected ?string $challenge;

    /**
     * @var string|null
     * @ORM\Column(type="string", length=64, options={"fixed"=true}, nullable=true)
     */
    protected ?string $salt;

    /**
     * @var string|null
     * @ORM\Column(type="string", length=256, nullable=true)
     */
    protected ?string $message;

    /**
     * @var DateTime|null
     * @ORM\Column(type="datetime", nullable=false)
     */
    protected ?DateTime $ttl;

    /**
     * @return $this
     */
    public function disable(): self
    {
        $this->ttl = new DateTime('1900-01-01 00:00:00');
        return $this;
    }

    /**
     * @param Token $token
     * @return $this
     */
    public function setToken(Token $token): self
    {
        $this->token = $token;
        return $this->disable();
    }
}
