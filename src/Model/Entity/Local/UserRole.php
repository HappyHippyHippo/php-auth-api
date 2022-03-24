<?php

namespace App\Model\Entity\Local;

use App\Repository\Local\UserRoleRepository;
use DateTime;
use Doctrine\ORM\Mapping as ORM;

/**
 * @method int|null getId()
 * @method UserRole setId(int|null $value)
 * @method bool|null isEnabled()
 * @method UserRole setEnabled(bool|null $value)
 * @method User|null getUser()
 * @method UserRole setUser(User|null $value)
 * @method Role|null getRole()
 * @method UserRole setRole(Role|null $value)
 * @method int|null getPriority()
 * @method UserRole setPriority(int|null $value)
 * @method DateTime|null getCreatedAt()
 * @method UserRole setCreatedAt(DateTime|null $value)
 * @method User|null getCreatedBy()
 * @method UserRole setCreatedBy(User|null $value)
 * @method DateTime|null getUpdatedAt()
 * @method UserRole setUpdatedAt(DateTime|null $value)
 * @method User|null getUpdatedBy()
 * @method UserRole setUpdatedBy(User|null $value)
 * @method DateTime|null getDeletedAt()
 * @method UserRole setDeletedAt(DateTime|null $value)
 * @method User|null getDeletedBy()
 * @method UserRole setDeletedBy(User|null $value)
 *
 * @ORM\Entity(repositoryClass=UserRoleRepository::class)
 * @ORM\Table(
 *      name="user_role",
 *      indexes={
 *          @ORM\Index(name="idx_user_role_user", columns={"user_id"})
 *      },
 *      uniqueConstraints={
 *          @ORM\Index(name="idx_user_role", columns={"user_id", "role_id"})
 *      })
 */
class UserRole extends Entity
{
    /**
     * @var bool|null
     * @ORM\Column(type="boolean", name="enabled", nullable=false)
     */
    protected ?bool $enabled;

    /**
     * @var User
     * @ORM\ManyToOne(targetEntity="User", cascade={"persist"})
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id", nullable=false)
     */
    protected User $user;

    /**
     * @var Role
     * @ORM\ManyToOne(targetEntity="Role", cascade={"persist"})
     * @ORM\JoinColumn(name="role_id", referencedColumnName="id", nullable=false)
     */
    protected Role $role;

    /**
     * @var int|null
     * @ORM\Column(type="integer", name="priority", nullable=false)
     */
    protected ?int $priority;

    /**
     * @return array<string, mixed>
     */
    public function jsonSerialize(): array
    {
        $this->addHideParser('user');
        $this->addHideParser('role');

        return array_merge($this->role->jsonSerialize(), parent::jsonSerialize());
    }
}
