<?php

namespace App\Model\Controller\RolePermissions;

use App\Model\Entity\Local\RolePermission;
use Hippy\Model\Model;

/**
 * @method RolePermission getPermission()
 * @method SingleResponse setPermission(RolePermission $value)
 */
class SingleResponse extends Model
{
    /**
     * @param RolePermission $permission
     */
    public function __construct(protected RolePermission $permission)
    {
        parent::__construct();
    }
}
