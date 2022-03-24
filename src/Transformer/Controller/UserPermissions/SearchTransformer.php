<?php

namespace App\Transformer\Controller\UserPermissions;

use Hippy\Api\Transformer\Validator\AbstractTransformer;

class SearchTransformer extends AbstractTransformer
{
    public function __construct()
    {
        parent::__construct([
            'headerRequestId' => 1,
            'headerAuthTokenId' => 2,
            'headerAuthUserId' => 3,
            'headerAuthUserEmail' => 4,
            'userId' => 10,
            'search' => 11,
            'start' => 12,
            'count' => 13
        ]);
    }
}
