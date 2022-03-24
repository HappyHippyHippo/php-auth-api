<?php

namespace App\Model\Controller\Users;

use Hippy\Api\Model\Controller\AuthRequestModel;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @method int[]|string|null getSearch()
 * @method int|null getStart()
 * @method int|null getCount()
 */
class SearchRequest extends AuthRequestModel
{
    /**
     * @var mixed
     * @Assert\AtLeastOneOf({
     *      @Assert\IsNull(message="search parameter is not present"),
     *      @Assert\Type(type="string", message="search parameter is a string"),
     *      @Assert\Sequentially({
     *          @Assert\Type(type="array", message="search parameter is an array"),
     *          @Assert\Count(min="1", minMessage="search parameter list cannot be empty"),
     *          @Assert\All({
     *              @Assert\NotBlank(message="search list parameter element must be present"),
     *              @Assert\Regex(pattern = "/^\d+$/", message="search list parameter element must be an integer"),
     *              @Assert\Positive(message="search list parameter element must be a positive integer")
     *          })
     *      }),
     * })
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

        $this->search = $this->searchBag($request->query, 'search');
        $this->start = $this->searchBagInt($request->query, 'start');
        $this->count = $this->searchBagInt($request->query, 'count');
    }
}
