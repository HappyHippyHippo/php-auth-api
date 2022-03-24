<?php

namespace App\Model\Controller\UserRoles;

use Hippy\Api\Model\Controller\AuthRequestModel;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @method int getUserId()
 * @method int getRoleId()
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
     * @Assert\Type(type="int", message="roleId parameter must be of type int")
     * @Assert\NotBlank(message="roleId parameter must be present")
     * @Assert\Positive(message="roleId parameter must be a positive integer")
     */
    protected mixed $roleId;

    /**
     * @param Request $request
     */
    public function __construct(Request $request)
    {
        parent::__construct($request);

        $this->userId = $this->searchBagInt($request->attributes, 'userId');
        $this->roleId = $this->searchBagInt($request->attributes, 'roleId');
    }
}
