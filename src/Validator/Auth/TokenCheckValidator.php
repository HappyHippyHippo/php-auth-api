<?php

namespace App\Validator\Auth;

use App\Model\Controller\Auth\CheckRequest;
use App\Transformer\Controller\Auth\TokenCheckTransformer;
use Hippy\Api\Validator\AbstractValidator;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class TokenCheckValidator extends AbstractValidator
{
    /**
     * @param ValidatorInterface $validator
     * @param TokenCheckTransformer $transformer
     */
    public function __construct(
        ValidatorInterface $validator,
        private TokenCheckTransformer $transformer,
    ) {
        parent::__construct($validator);
    }

    /**
     * @param Request $request
     * @return CheckRequest
     */
    public function validate(Request $request): CheckRequest
    {
        $request = new CheckRequest($request);
        $this->process($request, $this->transformer);
        return $request;
    }
}
