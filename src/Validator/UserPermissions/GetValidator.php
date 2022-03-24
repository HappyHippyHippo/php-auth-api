<?php

namespace App\Validator\UserPermissions;

use App\Model\Controller\UserPermissions\GetRequest;
use App\Transformer\Controller\UserPermissions\GetTransformer;
use Hippy\Api\Validator\AbstractValidator;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class GetValidator extends AbstractValidator
{
    /**
     * @param ValidatorInterface $validator
     * @param GetTransformer $transformer
     */
    public function __construct(
        ValidatorInterface $validator,
        private GetTransformer $transformer,
    ) {
        parent::__construct($validator);
    }

    /**
     * @param Request $request
     * @return GetRequest
     */
    public function validate(Request $request): GetRequest
    {
        $request = new GetRequest($request);
        $this->process($request, $this->transformer);
        return $request;
    }
}
