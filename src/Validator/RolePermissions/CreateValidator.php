<?php

namespace App\Validator\RolePermissions;

use App\Model\Controller\RolePermissions\CreateRequest;
use App\Transformer\Controller\RolePermissions\CreateTransformer;
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
