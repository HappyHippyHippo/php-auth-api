<?php

namespace App\Model\Entity\Local;

use App\Repository\Local\UserPermissionRepository;
use DateTime;
use Doctrine\ORM\Mapping as ORM;

/**
 * @method int|null getId()
 * @method UserPermission setId(int|null $value)
 * @method User|null getUser()
 * @method UserPermission setUser(User|null $value)
 * @method bool|null isEnabled()
 * @method UserPermission setEnabled(bool|null $value)
 * @method string|null getDirectory()
 * @method UserPermission setDirectory(string|null $value)
 * @method string|null getDescription()
 * @method UserPermission setDescription(string|null $value)
 * @method PermissionLevel|null getLevel()
 * @method UserPermission setLevel(PermissionLevel|null $value)
 * @method DateTime|null getCreatedAt()
 * @method UserPermission setCreatedAt(DateTime|null $value)
 * @method User|null getCreatedBy()
 * @method UserPermission setCreatedBy(User|null $value)
 * @method DateTime|null getUpdatedAt()
 * @method UserPermission setUpdatedAt(DateTime|null $value)
 * @method User|null getUpdatedBy()
 * @method UserPermission setUpdatedBy(User|null $value)
 * @method DateTime|null getDeletedAt()
 * @method UserPermission setDeletedAt(DateTime|null $value)
 * @method User|null getDeletedBy()
 * @method UserPermission setDeletedBy(User|null $value)
 *
 * @ORM\Entity(repositoryClass=UserPermissionRepository::class)
 * @ORM\Table(
 *      name="user_permission",
 *      uniqueConstraints={
 *          @ORM\Index(name="idx_user_permission_directory", columns={"user_id", "directory"})
 *      })
 */
class UserPermission extends Entity
{
    /**
     * @var bool|null
     * @ORM\Column(type="boolean", name="enabled", nullable=false)
     */
    protected ?bool $enabled;

    /**
     * @var User
     * @ORM\ManyToOne(targetEntity="User", inversedBy="permissions", cascade={"persist"})
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id", nullable=false)
     */
    protected User $user;

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
        $this->addHideParser('user');
        $this->addParser('level', function (PermissionLevel $level): string {
            return $level->value;
        });

        return parent::jsonSerialize();
    }
}
