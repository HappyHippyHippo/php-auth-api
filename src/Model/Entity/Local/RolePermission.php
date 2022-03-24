<?php

namespace App\Model\Entity\Local;

use App\Repository\Local\RolePermissionRepository;
use DateTime;
use Doctrine\ORM\Mapping as ORM;

/**
 * @method int|null getId()
 * @method RolePermission setId(int|null $value)
 * @method Role|null getRole()
 * @method RolePermission setRole(Role|null $value)
 * @method bool|null isEnabled()
 * @method RolePermission setEnabled(bool|null $value)
 * @method string|null getDirectory()
 * @method RolePermission setDirectory(string|null $value)
 * @method string|null getDescription()
 * @method RolePermission setDescription(string|null $value)
 * @method PermissionLevel|null getLevel()
 * @method RolePermission setLevel(PermissionLevel|null $value)
 * @method DateTime|null getCreatedAt()
 * @method RolePermission setCreatedAt(DateTime|null $value)
 * @method User|null getCreatedBy()
 * @method RolePermission setCreatedBy(User|null $value)
 * @method DateTime|null getUpdatedAt()
 * @method RolePermission setUpdatedAt(DateTime|null $value)
 * @method User|null getUpdatedBy()
 * @method RolePermission setUpdatedBy(User|null $value)
 * @method DateTime|null getDeletedAt()
 * @method RolePermission setDeletedAt(DateTime|null $value)
 * @method User|null getDeletedBy()
 * @method RolePermission setDeletedBy(User|null $value)
 *
 * @ORM\Entity(repositoryClass=RolePermissionRepository::class)
 * @ORM\Table(
 *      name="role_permission",
 *      uniqueConstraints={
 *          @ORM\Index(name="idx_role_permission_directory", columns={"role_id", "directory"})
 *      })
 */
class RolePermission extends Entity
{
    /**
     * @var bool|null
     * @ORM\Column(type="boolean", name="enabled", nullable=false)
     */
    protected ?bool $enabled;

    /**
     * @var Role
     * @ORM\ManyToOne(targetEntity="Role", inversedBy="permissions", cascade={"persist"})
     * @ORM\JoinColumn(name="role_id", referencedColumnName="id", nullable=false)
     */
    protected Role $role;

    /**
     * @var string|null
     * @ORM\Column(type="string", name="directory", nullable=false)
     */
    protected ?string $directory;

    /**
     * @var PermissionLevel|null
     * @ORM\Column(
     *     type="string",
     *     name="level",
     *     nullable=false,
     *     enumType=PermissionLevel::class
     * )
     */
    protected ?PermissionLevel $level;

    /**
     * @var string|null
     * @ORM\Column(type="string", name="description", nullable=true)
     */
    protected ?string $description;

    /**
     * @return array<string, string>
     */
    public function jsonSerialize(): array
    {
        $this->addHideParser('role');
        $this->addParser('level', function (PermissionLevel $level): string {
            return $level->value;
        });

        return parent::jsonSerialize();
    }
}
