<?php

namespace App\Validator\Users;

use App\Model\Controller\Users\EnableRequest;
use App\Transformer\Controller\Users\EnableTransformer;
use Hippy\Api\Validator\AbstractValidator;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class EnableValidator extends AbstractValidator
{
    /**
     * @param ValidatorInterface $validator
     * @param EnableTransformer $transformer
     */
    public function __construct(
        ValidatorInterface $validator,
        private EnableTransformer $transformer,
    ) {
        parent::__construct($validator);
    }

    /**
     * @param Request $request
     * @return EnableRequest
     */
    public function validate(Request $request): EnableRequest
    {
        $request = new EnableRequest($request);
        $this->process($request, $this->transformer);
        return $request;
    }
}
