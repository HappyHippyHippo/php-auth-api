<?php

namespace App\Model\Entity\Local;

use Hippy\Model\Collection;

class RolePermissionCollection extends Collection
{
    /**
     * @param User[] $items
     */
    public function __construct(array $items = [])
    {
        parent::__construct(RolePermission::class, $items);

        $this->identifier = function (RolePermission $permission) {
            return $permission->getId();
        };
    }
}
