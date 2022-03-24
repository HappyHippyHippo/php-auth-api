<?php

namespace App\Model\Controller\Roles;

use Hippy\Api\Model\Controller\AuthRequestModel;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @method bool isEnabled()
 * @method string getName()
 * @method string|null getDescription()
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
     * @Assert\Type(type="string", message="name parameter must be of type string")
     * @Assert\NotBlank(message="name parameter must be present")
     */
    protected mixed $name;

    /**
     * @var mixed
     * @Assert\Type(type="string", message="description parameter must be of type string")
     */
    protected mixed $description;

    /**
     * @param Request $request
     */
    public function __construct(Request $request)
    {
        parent::__construct($request);

        $this->enabled = $this->searchBagBool($request->request, 'enabled');
        $this->name = $this->searchBag($request->request, 'name');
        $this->description = $this->searchBag($request->request, 'description');
    }
}
