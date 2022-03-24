<?php

namespace App\Validator\RolePermissions;

use App\Model\Controller\RolePermissions\UpdateRequest;
use App\Transformer\Controller\RolePermissions\UpdateTransformer;
use Hippy\Api\Validator\AbstractValidator;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class UpdateValidator extends AbstractValidator
{
    /**
     * @param ValidatorInterface $validator
     * @param UpdateTransformer $transformer
     */
    public function __construct(
        ValidatorInterface $validator,
        private UpdateTransformer $transformer,
    ) {
        parent::__construct($validator);
    }

    /**
     * @param Request $request
     * @return UpdateRequest
     */
    public function validate(Request $request): UpdateRequest
    {
        $request = new UpdateRequest($request);
        $this->process($request, $this->transformer);
        return $request;
    }
}
