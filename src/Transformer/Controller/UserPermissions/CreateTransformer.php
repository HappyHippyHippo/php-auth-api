<?php

namespace App\Transformer\Controller\UserPermissions;

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
            'userId' => 10,
            'enabled' => 12,
            'directory' => 13,
            'level' => 14,
            'description' => 15,
        ]);
    }
}
