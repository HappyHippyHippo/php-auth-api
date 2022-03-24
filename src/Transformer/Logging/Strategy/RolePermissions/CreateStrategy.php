<?php

namespace App\Transformer\Logging\Strategy\RolePermissions;

use Hippy\Api\Transformer\Logging\Strategy\AbstractDefaultStrategy;
use Symfony\Component\HttpFoundation\Response;

class CreateStrategy extends AbstractDefaultStrategy
{
    public function __construct()
    {
        parent::__construct('role_permissions.create', Response::HTTP_CREATED);
    }
}
