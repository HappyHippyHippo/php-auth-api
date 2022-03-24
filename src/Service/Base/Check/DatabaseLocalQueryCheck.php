<?php

namespace App\Service\Base\Check;

use Doctrine\DBAL\Connection;
use Hippy\Api\Service\Base\Check\AbstractDatabaseQueryCheck;

class DatabaseLocalQueryCheck extends AbstractDatabaseQueryCheck
{
    /**
     * @param Connection $connection
     */
    public function __construct(Connection $connection)
    {
        parent::__construct('DB - local query check', $connection);
    }
}
