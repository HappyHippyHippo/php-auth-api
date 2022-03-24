<?php

namespace App\Model\Controller\Users;

use Hippy\Api\Model\Controller\AuthRequestModel;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @method int getUserId()
 * @method bool isEnabled()
 * @method string getEmail()
 */
class UpdateRequest extends AuthRequestModel
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
     * @Assert\Type(type="bool", message="enabled parameter must be of type bool")
     * @Assert\NotBlank(message="enabled parameter must be present")
     */
    protected mixed $enabled;

    /**
     * @var mixed
     * @Assert\Type(type="string", message="email parameter must be of type string")
     * @Assert\NotBlank(message="email parameter must be present")
     * @Assert\Email(mode = "loose", message="email parameter must be a valid email")
     */
    protected mixed $email;

    /**
     * @param Request $request
     */
    public function __construct(Request $request)
    {
        parent::__construct($request);

        $this->userId = $this->searchBagInt($request->attributes, 'userId');
        $this->enabled = $this->searchBagBool($request->request, 'enabled');
        $this->email = $this->searchBag($request->request, 'email');
    }
}
