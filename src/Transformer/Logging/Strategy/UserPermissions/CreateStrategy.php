<?php

namespace App\Transformer\Logging\Strategy\UserPermissions;

use Hippy\Api\Transformer\Logging\Strategy\AbstractDefaultStrategy;
use Symfony\Component\HttpFoundation\Response;

class CreateStrategy extends AbstractDefaultStrategy
{
    public function __construct()
    {
        parent::__construct('user_permissions.create', Response::HTTP_CREATED);
    }
}
