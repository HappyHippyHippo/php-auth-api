<?php

namespace App\Model\Entity\Local;

use Hippy\Model\Collection;

class UserPermissionCollection extends Collection
{
    /**
     * @param User[] $items
     */
    public function __construct(array $items = [])
    {
        parent::__construct(UserPermission::class, $items);

        $this->identifier = function (UserPermission $permission) {
            return $permission->getId();
        };
    }
}
