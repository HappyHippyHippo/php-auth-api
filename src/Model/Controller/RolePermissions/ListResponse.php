<?php

namespace App\Model\Controller\RolePermissions;

use App\Model\Entity\Local\RolePermissionCollection;
use Hippy\Api\Repository\ListReport;
use Hippy\Api\Repository\ListResult;
use Hippy\Model\Model;
use InvalidArgumentException;

/**
 * @method RolePermissionCollection getRoles()
 * @method ListResponse setRoles(RolePermissionCollection $value)
 * @method ListReport getReport()
 * @method ListResponse setReport(ListReport $value)
 */
class ListResponse extends Model
{
    /** @var RolePermissionCollection */
    protected RolePermissionCollection $permissions;

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
        if (!($collection instanceof RolePermissionCollection)) {
            throw new InvalidArgumentException('invalid collection type');
        }

        $this->permissions = $collection;
        $this->report = $result->getReport();
    }
}
