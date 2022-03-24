<?php

namespace App\Validator\Auth;

use App\Model\Controller\Auth\ChapRequestRequest;
use App\Transformer\Controller\Auth\AuthChapRequestTransformer;
use Hippy\Api\Validator\AbstractValidator;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class AuthChapRequestValidator extends AbstractValidator
{
    /**
     * @param ValidatorInterface $validator
     * @param AuthChapRequestTransformer $transformer
     */
    public function __construct(
        ValidatorInterface $validator,
        private AuthChapRequestTransformer $transformer,
    ) {
        parent::__construct($validator);
    }

    /**
     * @param Request $request
     * @return ChapRequestRequest
     */
    public function validate(Request $request): ChapRequestRequest
    {
        $request = new ChapRequestRequest($request);
        $this->process($request, $this->transformer);
        return $request;
    }
}
