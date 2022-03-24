<?php

namespace App\Model\Controller\Users;

use App\Model\Entity\Local\UserCollection;
use Hippy\Api\Repository\ListReport;
use Hippy\Api\Repository\ListResult;
use Hippy\Model\Model;
use InvalidArgumentException;

/**
 * @method UserCollection getUsers()
 * @method ListResponse setUsers(UserCollection $value)
 * @method ListReport getReport()
 * @method ListResponse setReport(ListReport $value)
 */
class ListResponse extends Model
{
    /** @var UserCollection */
    protected UserCollection $users;

    /** @var ListReport */
    protected ListReport $report;

    /**
     * @param ListResult $result
     * @throws InvalidArgumentException
     */
    public function __construct(ListResult $result)
    {
        parent::__construct();

        $collection = $result->getCollection();
        if (!($collection instanceof UserCollection)) {
            throw new InvalidArgumentException('invalid collection type');
        }

        $this->users = $collection;
        $this->report = $result->getReport();
    }
}
