<?php

namespace App\Model\Entity\Local;

use Hippy\Model\Collection;

class RoleCollection extends Collection
{
    /**
     * @param User[] $items
     */
    public function __construct(array $items = [])
    {
        parent::__construct(Role::class, $items);

        $this->identifier = function (Role $role) {
            return $role->getId();
        };
    }
}
