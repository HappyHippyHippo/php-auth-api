<?php

namespace App\Model\Controller\Auth;

use Hippy\Api\Model\Controller\RequestModel;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @method string getJwt()
 */
class CheckRequest extends RequestModel
{
    /**
     * @var mixed
     * @Assert\Type(type="string", message="jwt parameter must be of type string")
     * @Assert\NotBlank(message="jwt parameter must be present")
     */
    protected mixed $jwt;

    /**
     * @param Request $request
     */
    public function __construct(Request $request)
    {
        parent::__construct($request);

        $this->jwt = $this->searchBag($request->query, 'jwt');
    }
}
