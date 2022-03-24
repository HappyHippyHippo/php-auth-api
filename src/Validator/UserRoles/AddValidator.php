<?php

namespace App\Validator\UserRoles;

use App\Model\Controller\UserRoles\AddRequest;
use App\Transformer\Controller\UserRoles\AddTransformer;
use Hippy\Api\Validator\AbstractValidator;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class AddValidator extends AbstractValidator
{
    /**
     * @param ValidatorInterface $validator
     * @param AddTransformer $transformer
     */
    public function __construct(
        ValidatorInterface $validator,
        private AddTransformer $transformer,
    ) {
        parent::__construct($validator);
    }

    /**
     * @param Request $request
     * @return AddRequest
     */
    public function validate(Request $request): AddRequest
    {
        $request = new AddRequest($request);
        $this->process($request, $this->transformer);
        return $request;
    }
}
