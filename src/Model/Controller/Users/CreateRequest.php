<?php

namespace App\Model\Controller\Users;

use Hippy\Api\Model\Controller\AuthRequestModel;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @method bool isEnabled()
 * @method string getEmail()
 * @method string getPassword()
 */
class CreateRequest extends AuthRequestModel
{
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
     * @Assert\Email(mode="loose", message="email parameter must be a valid email")
     */
    protected mixed $email;

    /**
     * @var mixed
     * @Assert\Type(type="string", message="password parameter must be of type string")
     * @Assert\NotBlank(message="password parameter must be present")
     */
    protected mixed $password;

    /**
     * @param Request $request
     */
    public function __construct(Request $request)
    {
        parent::__construct($request);

        $this->enabled = $this->searchBagBool($request->request, 'enabled');
        $this->email = $this->searchBag($request->request, 'email');
        $this->password = $this->searchBag($request->request, 'password');
    }
}
