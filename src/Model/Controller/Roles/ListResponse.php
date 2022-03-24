<?php

namespace App\Model\Controller\Roles;

use App\Model\Entity\Local\RoleCollection;
use Hippy\Api\Repository\ListReport;
use Hippy\Api\Repository\ListResult;
use Hippy\Model\Model;
use InvalidArgumentException;

/**
 * @method RoleCollection getRoles()
 * @method ListResponse setRoles(RoleCollection $value)
 * @method ListReport getReport()
 * @method ListResponse setReport(ListReport $value)
 */
class ListResponse extends Model
{
    /** @var RoleCollection */
    protected RoleCollection $roles;

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
        if (!($collection instanceof RoleCollection)) {
            throw new InvalidArgumentException('invalid collection type');
        }

        $this->roles = $collection;
        $this->report = $result->getReport();
    }
}
