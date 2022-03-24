<?php

namespace App\Model\Controller\Roles;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @method int getRoleId()
 */
class UpdateRequest extends CreateRequest
{
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

        $this->roleId = $this->searchBagInt($request->attributes, 'roleId');
    }
}
