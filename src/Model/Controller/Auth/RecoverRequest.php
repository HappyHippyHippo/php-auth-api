<?php

namespace App\Model\Controller\Auth;

use Hippy\Api\Model\Controller\RequestModel;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @method string getJwt()
 * @method string getRecover()
 */
class RecoverRequest extends RequestModel
{
    /**
     * @var mixed
     * @Assert\Type(type="string", message="jwt parameter must be of type string")
     * @Assert\NotBlank(message="jwt parameter must be present")
     */
    protected mixed $jwt;

    /**
     * @var mixed
     * @Assert\Type(type="string", message="recover parameter must be of type string")
     * @Assert\NotBlank(message="recover parameter must be present")
     */
    protected mixed $recover;

    /**
     * @param Request $request
     */
    public function __construct(Request $request)
    {
        parent::__construct($request);

        $this->jwt = $this->searchBag($request->request, 'jwt');
        $this->recover = $this->searchBag($request->request, 'recover');
    }
}
