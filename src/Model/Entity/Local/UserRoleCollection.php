<?php

namespace App\Model\Entity\Local;

use Hippy\Model\Collection;

class UserRoleCollection extends Collection
{
    /**
     * @param UserRole[] $items
     */
    public function __construct(array $items = [])
    {
        parent::__construct(UserRole::class, $items);

        $this->identifier = function (UserRole $rel) {
            return $rel->getRole()?->getName();
        };
    }
}
