<?php

namespace App\Validator\RolePermissions;

use App\Model\Controller\RolePermissions\DisableRequest;
use App\Transformer\Controller\RolePermissions\DisableTransformer;
use Hippy\Api\Validator\AbstractValidator;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class DisableValidator extends AbstractValidator
{
    /**
     * @param ValidatorInterface $validator
     * @param DisableTransformer $transformer
     */
    public function __construct(
        ValidatorInterface $validator,
        private DisableTransformer $transformer,
    ) {
        parent::__construct($validator);
    }

    /**
     * @param Request $request
     * @return DisableRequest
     */
    public function validate(Request $request): DisableRequest
    {
        $request = new DisableRequest($request);
        $this->process($request, $this->transformer);
        return $request;
    }
}
