<?php

namespace App\Model\Entity\Local;

use App\Repository\Local\RoleRepository;
use DateTime;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @method int|null getId()
 * @method Role setId(int|null $value)
 * @method bool|null isEnabled()
 * @method Role setEnabled(bool|null $value)
 * @method string|null getName()
 * @method Role setName(string|null $value)
 * @method string|null getDescription()
 * @method Role setDescription(string|null $value)
 * @method Collection<int, RolePermission>|null getPermissions()
 * @method DateTime|null getCreatedAt()
 * @method Role setCreatedAt(DateTime|null $value)
 * @method User|null getCreatedBy()
 * @method Role setCreatedBy(User|null $value)
 * @method DateTime|null getUpdatedAt()
 * @method Role setUpdatedAt(DateTime|null $value)
 * @method User|null getUpdatedBy()
 * @method Role setUpdatedBy(User|null $value)
 * @method DateTime|null getDeletedAt()
 * @method Role setDeletedAt(DateTime|null $value)
 * @method User|null getDeletedBy()
 * @method Role setDeletedBy(User|null $value)
 *
 * @ORM\Entity(repositoryClass=RoleRepository::class)
 * @ORM\Table(
 *      name="role",
 *      uniqueConstraints={
 *          @ORM\Index(name="idx_role_name", columns={"name"})
 *      })
 */
class Role extends Entity
{
    /**
     * @var bool|null
     * @ORM\Column(type="boolean", name="enabled", nullable=false)
     */
    protected ?bool $enabled;

    /**
     * @var string|null
     * @ORM\Column(type="string", name="name", nullable=false)
     */
    protected ?string $name;

    /**
     * @var string|null
     * @ORM\Column(type="string", name="description", nullable=true)
     */
    protected ?string $description;

    /**
     * @var Collection<int, RolePermission>
     * @ORM\OneToMany (targetEntity="RolePermission", mappedBy="role", cascade={"persist"})
     */
    protected Collection $permissions;

    public function jsonSerialize(): array
    {
        $this->addHideParser('permissions');

        return parent::jsonSerialize();
    }
}
