<?php

namespace App\Model\Entity\Local;

use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Hippy\Model\Model;

class Entity extends Model
{
    /**
     * @var int|null
     * @ORM\Id()
     * @ORM\GeneratedValue
     * @ORM\Column(type="bigint", name="id", options={"unsigned":true})
     */
    protected ?int $id;

    /**
     * @var DateTime|null
     * @ORM\Column(type="datetime", name="created_at", nullable=false)
     */
    protected ?DateTime $createdAt;

    /**
     * @var User|null
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumn(name="created_by_id", referencedColumnName="id")
     */
    protected ?User $createdBy;

    /**
     * @var DateTime|null
     * @ORM\Column(type="datetime", name="updated_at", nullable=false)
     */
    protected ?DateTime $updatedAt;

    /**
     * @var User|null
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumn(name="updated_by_id", referencedColumnName="id")
     */
    protected ?User $updatedBy;

    /**
     * @var DateTime|null
     * @ORM\Column(type="datetime", name="deleted_at", nullable=true)
     */
    protected ?DateTime $deletedAt;

    /**
     * @var User|null
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumn(name="deleted_by_id", referencedColumnName="id")
     */
    protected ?User $deletedBy;

    /**
     * @return array<string, string>
     */
    public function jsonSerialize(): array
    {
        $this->addDateTimeParser('createdAt');
        $this->addParser('createdBy', function (?User $user) {
            return $user?->getEmail();
        });
        $this->addDateTimeParser('updatedAt');
        $this->addParser('updatedBy', function (?User $user) {
            return $user?->getEmail();
        });
        $this->addDateTimeParser('deletedAt');
        $this->addParser('deletedBy', function (?User $user) {
            return $user?->getEmail();
        });

        foreach (array_keys(get_object_vars($this)) as $name) {
            if (str_starts_with($name, '_')) {
                $this->addHideParser($name);
            }
        }

        return parent::jsonSerialize();
    }
}
