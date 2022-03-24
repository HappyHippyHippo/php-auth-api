<?php

namespace App\Validator\UserRoles;

use App\Model\Controller\UserRoles\PriorityRequest;
use App\Transformer\Controller\UserRoles\PriorityTransformer;
use Hippy\Api\Validator\AbstractValidator;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class PriorityValidator extends AbstractValidator
{
    /**
     * @param ValidatorInterface $validator
     * @param PriorityTransformer $transformer
     */
    public function __construct(
        ValidatorInterface $validator,
        private PriorityTransformer $transformer,
    ) {
        parent::__construct($validator);
    }

    /**
     * @param Request $request
     * @return PriorityRequest
     */
    public function validate(Request $request): PriorityRequest
    {
        $request = new PriorityRequest($request);
        $this->process($request, $this->transformer);
        return $request;
    }
}
