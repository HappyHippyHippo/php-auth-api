<?php

namespace App\Transformer\Controller\Auth;

use Hippy\Api\Transformer\Validator\AbstractTransformer;

class TokenCheckTransformer extends AbstractTransformer
{
    public function __construct()
    {
        parent::__construct(['jwt' => 1]);
    }
}
