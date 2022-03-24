<?php

namespace App\Model\Controller\UserPermissions;

use App\Model\Entity\Local\UserPermissionCollection;
use Hippy\Api\Repository\ListReport;
use Hippy\Api\Repository\ListResult;
use Hippy\Model\Model;
use InvalidArgumentException;

/**
 * @method UserPermissionCollection getUsers()
 * @method ListResponse setUsers(UserPermissionCollection $value)
 * @method ListReport getReport()
 * @method ListResponse setReport(ListReport $value)
 */
class ListResponse extends Model
{
    /** @var UserPermissionCollection */
    protected UserPermissionCollection $permissions;

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
        if (!($collection instanceof UserPermissionCollection)) {
            throw new InvalidArgumentException('invalid collection type');
        }

        $this->permissions = $collection;
        $this->report = $result->getReport();
    }
}
