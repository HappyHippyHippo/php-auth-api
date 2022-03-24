<?php

namespace App\Model\Controller\Roles;

use App\Model\Entity\Local\Role;
use Hippy\Model\Model;

/**
 * @method Role getRole()
 * @method SingleResponse setRole(Role $value)
 */
class SingleResponse extends Model
{
    /**
     * @param Role $role
     */
    public function __construct(protected Role $role)
    {
        parent::__construct();
    }
}
