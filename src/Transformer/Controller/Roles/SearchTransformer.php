<?php

namespace App\Transformer\Controller\Roles;

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
            'search' => 10,
            'start' => 11,
            'count' => 12
        ]);
    }
}
