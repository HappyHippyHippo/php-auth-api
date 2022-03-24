<?php

namespace App\Transformer\Controller\Roles;

use Hippy\Api\Transformer\Validator\AbstractTransformer;

class GetTransformer extends AbstractTransformer
{
    public function __construct()
    {
        parent::__construct([
            'headerRequestId' => 1,
            'headerAuthTokenId' => 2,
            'headerAuthUserId' => 3,
            'headerAuthUserEmail' => 4,
            'roleId' => 10,
        ]);
    }
}
