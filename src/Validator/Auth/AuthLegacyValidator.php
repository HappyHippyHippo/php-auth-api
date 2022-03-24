<?php

namespace App\Validator\Auth;

use App\Model\Controller\Auth\LegacyRequest;
use App\Transformer\Controller\Auth\AuthLegacyTransformer;
use Hippy\Api\Validator\AbstractValidator;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class AuthLegacyValidator extends AbstractValidator
{
    /**
     * @param ValidatorInterface $validator
     * @param AuthLegacyTransformer $transformer
     */
    public function __construct(
        ValidatorInterface $validator,
        private AuthLegacyTransformer $transformer,
    ) {
        parent::__construct($validator);
    }

    /**
     * @param Request $request
     * @return LegacyRequest
     */
    public function validate(Request $request): LegacyRequest
    {
        $request = new LegacyRequest($request);
        $this->process($request, $this->transformer);
        return $request;
    }
}
