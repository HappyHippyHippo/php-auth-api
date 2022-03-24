<?php

namespace App\Model\Controller\UserRoles;

use App\Model\Entity\Local\UserRoleCollection;
use Hippy\Model\Model;
use InvalidArgumentException;

/**
 * @method UserRoleCollection getRoles()
 * @method ListResponse setRoles(UserRoleCollection $value)
 */
class ListResponse extends Model
{
    /**
     * @param UserRoleCollection $roles
     * @throws InvalidArgumentException
     */
    public function __construct(protected UserRoleCollection $roles)
    {
        parent::__construct();
    }
}
