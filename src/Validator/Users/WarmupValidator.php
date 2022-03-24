<?php

namespace App\Validator\Users;

use App\Model\Controller\Users\WarmupRequest;
use App\Transformer\Controller\Users\WarmupTransformer;
use Hippy\Api\Validator\AbstractValidator;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class WarmupValidator extends AbstractValidator
{
    /**
     * @param ValidatorInterface $validator
     * @param WarmupTransformer $transformer
     */
    public function __construct(
        ValidatorInterface $validator,
        private WarmupTransformer $transformer,
    ) {
        parent::__construct($validator);
    }

    /**
     * @param Request $request
     * @return WarmupRequest
     */
    public function validate(Request $request): WarmupRequest
    {
        $request = new WarmupRequest($request);
        $this->process($request, $this->transformer);
        return $request;
    }
}
