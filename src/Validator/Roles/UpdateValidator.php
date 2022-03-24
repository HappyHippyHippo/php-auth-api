<?php

namespace App\Validator\Roles;

use App\Model\Controller\Roles\UpdateRequest;
use App\Transformer\Controller\Roles\UpdateTransformer;
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
