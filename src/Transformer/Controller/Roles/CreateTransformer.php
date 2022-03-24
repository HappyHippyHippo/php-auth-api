<?php

namespace App\Transformer\Controller\Roles;

use Hippy\Api\Transformer\Validator\AbstractTransformer;

class CreateTransformer extends AbstractTransformer
{
    public function __construct()
    {
        parent::__construct([
            'headerRequestId' => 1,
            'headerAuthTokenId' => 2,
            'headerAuthUserId' => 3,
            'headerAuthUserEmail' => 4,
            'enabled' => 11,
            'name' => 12,
            'description' => 13,
        ]);
    }
}
