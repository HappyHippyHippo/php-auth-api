<?php

namespace App\Validator\Roles;

use App\Model\Controller\Roles\CreateRequest;
use App\Transformer\Controller\Roles\CreateTransformer;
use Hippy\Api\Validator\AbstractValidator;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class CreateValidator extends AbstractValidator
{
    /**
     * @param ValidatorInterface $validator
     * @param CreateTransformer $transformer
     */
    public function __construct(
        ValidatorInterface $validator,
        private CreateTransformer $transformer,
    ) {
        parent::__construct($validator);
    }

    /**
     * @param Request $request
     * @return CreateRequest
     */
    public function validate(Request $request): CreateRequest
    {
        $request = new CreateRequest($request);
        $this->process($request, $this->transformer);
        return $request;
    }
}
