<?php

namespace App\Validator\Auth;

use App\Model\Controller\Auth\RecoverRequest;
use App\Transformer\Controller\Auth\TokenRecoverTransformer;
use Hippy\Api\Validator\AbstractValidator;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class TokenRecoverValidator extends AbstractValidator
{
    /**
     * @param ValidatorInterface $validator
     * @param TokenRecoverTransformer $transformer
     */
    public function __construct(
        ValidatorInterface $validator,
        private TokenRecoverTransformer $transformer,
    ) {
        parent::__construct($validator);
    }

    /**
     * @param Request $request
     * @return RecoverRequest
     */
    public function validate(Request $request): RecoverRequest
    {
        $request = new RecoverRequest($request);
        $this->process($request, $this->transformer);
        return $request;
    }
}
