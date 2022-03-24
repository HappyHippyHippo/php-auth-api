<?php

namespace App\Transformer\Logging\Strategy\Auth;

use Hippy\Api\Transformer\Logging\Strategy\AbstractDefaultStrategy;
use Symfony\Component\HttpFoundation\Response;

class AuthChapResponseStrategy extends AbstractDefaultStrategy
{
    public function __construct()
    {
        parent::__construct('auth.chap.response', Response::HTTP_CREATED);
    }
}
