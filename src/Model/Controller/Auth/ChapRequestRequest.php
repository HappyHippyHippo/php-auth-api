<?php

namespace App\Model\Controller\Auth;

use Hippy\Api\Model\Controller\RequestModel;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @method string getEmail()
 */
class ChapRequestRequest extends RequestModel
{
    /**
     * @var string|null
     * @Assert\Type(type="string", message="email parameter must be of type string")
     * @Assert\NotBlank(message="email parameter must be present")
     * @Assert\Email(message="email parameter must be a valid email")
     */
    protected mixed $email;

    /**
     * @param Request $request
     */
    public function __construct(Request $request)
    {
        parent::__construct($request);

        $this->email = $this->searchBag($request->query, 'email');
    }
}
