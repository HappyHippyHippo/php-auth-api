<?php

namespace App\Model\Controller\RolePermissions;

use Hippy\Api\Model\Controller\AuthRequestModel;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @method int getRoleId()
 * @method int getPermissionId()
 */
class DisableRequest extends AuthRequestModel
{
    /**
     * @var mixed
     * @Assert\Type(type="int", message="roleId parameter must be of type int")
     * @Assert\NotBlank(message="roleId parameter must be present")
     * @Assert\Positive(message="roleId parameter must be a positive integer")
     */
    protected mixed $roleId;

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

        $this->roleId = $this->searchBagInt($request->attributes, 'roleId');
        $this->permissionId = $this->searchBagInt($request->attributes, 'permissionId');
    }
}
