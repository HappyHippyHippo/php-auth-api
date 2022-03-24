<?php

namespace App\Transformer\Controller\Auth;

use Hippy\Api\Transformer\Validator\AbstractTransformer;

class AuthChapResponseTransformer extends AbstractTransformer
{
    public function __construct()
    {
        parent::__construct(['email' => 1, 'challenge' => 2, 'response' => 3]);
    }
}
