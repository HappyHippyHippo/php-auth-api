<?php

namespace App\Transformer\Controller\UserRoles;

use Hippy\Api\Transformer\Validator\AbstractTransformer;

class PriorityTransformer extends AbstractTransformer
{
    public function __construct()
    {
        parent::__construct([
            'headerRequestId' => 1,
            'headerAuthTokenId' => 2,
            'headerAuthUserId' => 3,
            'headerAuthUserEmail' => 4,
            'userId' => 10,
            'roleId' => 11,
            'priority' => 12,
        ]);
    }
}
