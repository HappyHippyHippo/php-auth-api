<?php

namespace App\Model\Controller\UserPermissions;

use Hippy\Api\Model\Controller\AuthRequestModel;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @method int getUserId()
 * @method string|null getSearch()
 * @method int|null getStart()
 * @method int|null getCount()
 */
class SearchRequest extends AuthRequestModel
{
    /**
     * @var mixed
     * @Assert\Type(type="int", message="userId parameter must be of type int")
     * @Assert\NotBlank(message="userId parameter must be present")
     * @Assert\Positive(message="userId parameter must be a positive integer")
     */
    protected mixed $userId;

    /**
     * @var mixed
     * @Assert\Type(type="string", message="search parameter must be of type string")
     */
    protected mixed $search;

    /**
     * @var mixed
     * @Assert\Type(type="int", message="start parameter must be of type int")
     * @Assert\PositiveOrZero(message="start parameter must be a zero or positive integer")
     */
    protected mixed $start;

    /**
     * @var mixed
     * @Assert\Type(type="int", message="count parameter must be of type int")
     * @Assert\Positive(message="count parameter must be a positive integer")
     */
    protected mixed $count;

    /**
     * @param Request $request
     */
    public function __construct(Request $request)
    {
        parent::__construct($request);

        $this->userId = $this->searchBagInt($request->attributes, 'userId');
        $this->search = $this->searchBag($request->query, 'search');
        $this->start = $this->searchBagInt($request->query, 'start');
        $this->count = $this->searchBagInt($request->query, 'count');
    }
}
