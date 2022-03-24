<?php

namespace App\Model\Entity\Local;

use App\Repository\Local\UserRepository;
use DateTime;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @method int|null getId()
 * @method User setId(int|null $value)
 * @method bool|null isEnabled()
 * @method User setEnabled(bool|null $value)
 * @method string|null getEmail()
 * @method User setEmail(string|null $value)
 * @method string|null getPassword()
 * @method User setPassword(string|null $value)
 * @method string|null getSalt()
 * @method User setSalt(string|null $value)
 * @method int|null getTries()
 * @method User setTries(int|null $value)
 * @method DateTime|null getCoolDown()
 * @method User setCoolDown(DateTime|null $value)
 * @method Collection<int, UserPermission>|null getPermissions()
 * @method DateTime|null getCreatedAt()
 * @method User setCreatedAt(DateTime|null $value)
 * @method User|null getCreatedBy()
 * @method User setCreatedBy(User|null $value)
 * @method DateTime|null getUpdatedAt()
 * @method User setUpdatedAt(DateTime|null $value)
 * @method User|null getUpdatedBy()
 * @method User setUpdatedBy(User|null $value)
 * @method DateTime|null getDeletedAt()
 * @method User setDeletedAt(DateTime|null $value)
 * @method User|null getDeletedBy()
 * @method User setDeletedBy(User|null $value)
 *
 * @ORM\Entity(repositoryClass=UserRepository::class)
 * @ORM\Table(
 *      name="user",
 *      uniqueConstraints={
 *          @ORM\Index(name="idx_user_email", columns={"email"})
 *      })
 */
class User extends Entity
{
    /**
     * @var bool|null
     * @ORM\Column(type="boolean", name="enabled", nullable=false)
     */
    protected ?bool $enabled;

    /**
     * @var string|null
     * @ORM\Column(type="string", name="email", nullable=false)
     */
    protected ?string $email;

    /**
     * @var string|null
     * @ORM\Column(type="string", name="password", nullable=false)
     */
    protected ?string $password;

    /**
     * @var string|null
     * @ORM\Column(type="string", name="salt", nullable=false)
     */
    protected ?string $salt;

    /**
     * @var int|null
     * @ORM\Column(type="integer", name="tries", nullable=false)
     */
    protected ?int $tries;

    /**
     * @var DateTime|null
     * @ORM\Column(type="datetime", name="cool_down", nullable=false)
     */
    protected ?DateTime $coolDown;

    /**
     * @var Collection<int, UserPermission>
     * @ORM\OneToMany (targetEntity="UserPermission", mappedBy="user", cascade={"persist"})
     */
    protected Collection $permissions;

    /**
     * @return $this
     */
    public function decrementTries(): User
    {
        $this->tries = max(0, $this->tries - 1);

        return $this;
    }

    /**
     * @return array<string, string>
     */
    public function jsonSerialize(): array
    {
        $this->addHideParser('password');
        $this->addHideParser('salt');
        $this->addHideParser('permissions');
        $this->addDateTimeParser('coolDown');

        return parent::jsonSerialize();
    }
}
