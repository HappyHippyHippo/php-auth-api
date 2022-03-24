<?php

namespace App\Transformer\Logging\Strategy\Auth;

use Hippy\Api\Transformer\Logging\Strategy\AbstractDefaultStrategy;
use Symfony\Component\HttpFoundation\Response;

class TokenCheckStrategy extends AbstractDefaultStrategy
{
    public function __construct()
    {
        parent::__construct('auth.token.check', Response::HTTP_NO_CONTENT);
    }
}
