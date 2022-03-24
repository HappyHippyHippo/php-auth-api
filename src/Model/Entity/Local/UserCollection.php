<?php

namespace App\Model\Entity\Local;

use Hippy\Model\Collection;

class UserCollection extends Collection
{
    /**
     * @param User[] $items
     */
    public function __construct(array $items = [])
    {
        parent::__construct(User::class, $items);

        $this->identifier = function (User $user) {
            return $user->getId();
        };
    }
}
