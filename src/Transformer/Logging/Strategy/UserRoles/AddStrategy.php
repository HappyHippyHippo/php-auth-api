<?php

namespace App\Transformer\Logging\Strategy\UserRoles;

use Hippy\Api\Transformer\Logging\Strategy\AbstractDefaultStrategy;
use Symfony\Component\HttpFoundation\Response;

class AddStrategy extends AbstractDefaultStrategy
{
    public function __construct()
    {
        parent::__construct('users.roles.add', Response::HTTP_CREATED);
    }
}
