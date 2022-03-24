<?php

namespace App\Model\Controller\UserPermissions;

use App\Model\Entity\Local\UserPermission;
use Hippy\Model\Model;

/**
 * @method UserPermission getPermission()
 * @method SingleResponse setPermission(UserPermission $value)
 */
class SingleResponse extends Model
{
    /**
     * @param UserPermission $permission
     */
    public function __construct(protected UserPermission $permission)
    {
        parent::__construct();
    }
}
