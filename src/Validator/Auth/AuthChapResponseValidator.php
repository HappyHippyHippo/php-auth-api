<?php

namespace App\Validator\Auth;

use App\Model\Controller\Auth\ChapResponseRequest;
use App\Transformer\Controller\Auth\AuthChapResponseTransformer;
use Hippy\Api\Validator\AbstractValidator;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class AuthChapResponseValidator extends AbstractValidator
{
    /**
     * @param ValidatorInterface $validator
     * @param AuthChapResponseTransformer $transformer
     */
    public function __construct(
        ValidatorInterface $validator,
        private AuthChapResponseTransformer $transformer,
    ) {
        parent::__construct($validator);
    }

    /**
     * @param Request $request
     * @return ChapResponseRequest
     */
    public function validate(Request $request): ChapResponseRequest
    {
        $request = new ChapResponseRequest($request);
        $this->process($request, $this->transformer);
        return $request;
    }
}
