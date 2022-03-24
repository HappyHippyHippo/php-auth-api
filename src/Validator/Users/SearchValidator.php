<?php

namespace App\Validator\Users;

use App\Model\Controller\Users\SearchRequest;
use App\Transformer\Controller\Users\SearchTransformer;
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
