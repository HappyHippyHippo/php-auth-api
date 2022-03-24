<?php

namespace App\Transformer\Controller\Auth;

use Hippy\Api\Transformer\Validator\AbstractTransformer;

class AuthLegacyTransformer extends AbstractTransformer
{
    public function __construct()
    {
        parent::__construct(['email' => 1, 'password' => 2]);
    }
}
