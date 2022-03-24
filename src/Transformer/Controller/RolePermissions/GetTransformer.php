<?php

namespace App\Transformer\Controller\RolePermissions;

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
            'permissionId' => 11,
        ]);
    }
}
