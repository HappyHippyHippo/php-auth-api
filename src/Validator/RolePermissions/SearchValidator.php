<?php

namespace App\Validator\RolePermissions;

use App\Model\Controller\RolePermissions\SearchRequest;
use App\Transformer\Controller\RolePermissions\SearchTransformer;
use Hippy\Api\Validator\AbstractValidator;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class SearchValidator extends AbstractValidator
{
    /**
     * @param ValidatorInterface $validator
     * @param SearchTransformer $transformer
     */
    public function __construct(
        ValidatorInterface $validator,
        private SearchTransformer $transformer,
    ) {
        parent::__construct($validator);
    }

    /**
     * @param Request $request
     * @return SearchRequest
     */
    public function validate(Request $request): SearchRequest
    {
        $request = new SearchRequest($request);
        $this->process($request, $this->transformer);
        return $request;
    }
}
