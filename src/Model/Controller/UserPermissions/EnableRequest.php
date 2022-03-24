<?php

namespace App\Model\Controller\UserPermissions;

use Hippy\Api\Model\Controller\AuthRequestModel;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @method int getUserId()
 * @method int getPermissionId()
 */
class EnableRequest extends AuthRequestModel
{
    /**
     * @var mixed
     * @Assert\Type(type="int", message="userId parameter must be of type int")
     * @Assert\NotBlank(message="userId parameter must be present")
     * @Assert\Positive(message="userId parameter must be a positive integer")
     */
    protected mixed $userId;

    /**
     * @var mixed
     * @Assert\Type(type="int", message="permissionId parameter must be of type int")
     * @Assert\NotBlank(message="permissionId parameter must be present")
     * @Assert\Positive(message="permissionId parameter must be a positive integer")
     */
    protected mixed $permissionId;

    /**
     * @param Request $request
     */
    public function __construct(Request $request)
    {
        parent::__construct($request);

        $this->userId = $this->searchBagInt($request->attributes, 'userId');
        $this->permissionId = $this->searchBagInt($request->attributes, 'permissionId');
    }
}
