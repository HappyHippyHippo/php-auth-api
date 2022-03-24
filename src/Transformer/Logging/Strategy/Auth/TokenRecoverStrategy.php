<?php

namespace App\Transformer\Logging\Strategy\Auth;

use Hippy\Api\Transformer\Logging\Strategy\AbstractDefaultStrategy;
use Symfony\Component\HttpFoundation\Response;

class TokenRecoverStrategy extends AbstractDefaultStrategy
{
    public function __construct()
    {
        parent::__construct('auth.token.recover', Response::HTTP_CREATED);
    }
}
