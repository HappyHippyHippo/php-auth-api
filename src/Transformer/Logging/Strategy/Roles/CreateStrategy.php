<?php

namespace App\Transformer\Logging\Strategy\Roles;

use Hippy\Api\Transformer\Logging\Strategy\AbstractDefaultStrategy;
use Symfony\Component\HttpFoundation\Response;

class CreateStrategy extends AbstractDefaultStrategy
{
    public function __construct()
    {
        parent::__construct('roles.create', Response::HTTP_CREATED);
    }
}
