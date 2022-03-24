<?php

namespace App\Model\Controller\Auth;

use Hippy\Api\Model\Controller\RequestModel;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @method string getEmail()
 * @method string getChallenge()
 * @method string getResponse()
 */
class ChapResponseRequest extends RequestModel
{
    /**
     * @var mixed
     * @Assert\Type(type="string", message="email parameter must be of type string")
     * @Assert\NotBlank(message="email parameter must be present")
     * @Assert\Email(message="email parameter must be a valid email")
     */
    protected mixed $email;

    /**
     * @var mixed
     * @Assert\Type(type="string", message="challenge parameter must be of type string")
     * @Assert\NotBlank(message="challenge parameter must be present")
     */
    protected mixed $challenge;

    /**
     * @var mixed
     * @Assert\Type(type="string", message="response parameter must be of type string")
     * @Assert\NotBlank(message="response parameter must be present")
     */
    protected mixed $response;

    /**
     * @param Request $request
     */
    public function __construct(Request $request)
    {
        parent::__construct($request);

        $this->email = $this->searchBag($request->request, 'email');
        $this->challenge = $this->searchBag($request->request, 'challenge');
        $this->response = $this->searchBag($request->request, 'response');
    }
}
