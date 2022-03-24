<?php

namespace App\Validator\UserRoles;

use App\Model\Controller\UserRoles\RemoveRequest;
use App\Transformer\Controller\UserRoles\RemoveTransformer;
use Hippy\Api\Validator\AbstractValidator;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class RemoveValidator extends AbstractValidator
{
    /**
     * @param ValidatorInterface $validator
     * @param RemoveTransformer $transformer
     */
    public function __construct(
        ValidatorInterface $validator,
        private RemoveTransformer $transformer,
    ) {
        parent::__construct($validator);
    }

    /**
     * @param Request $request
     * @return RemoveRequest
     */
    public function validate(Request $request): RemoveRequest
    {
        $request = new RemoveRequest($request);
        $this->process($request, $this->transformer);
        return $request;
    }
}
