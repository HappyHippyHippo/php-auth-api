<?php

namespace App\Model\Controller\UserRoles;

use App\Model\Entity\Local\UserRole;
use Hippy\Model\Model;

/**
 * @method UserRole getRole()
 * @method SingleResponse setRole(UserRole $value)
 */
class SingleResponse extends Model
{
    /**
     * @param UserRole $role
     */
    public function __construct(protected UserRole $role)
    {
        parent::__construct();
    }
}
