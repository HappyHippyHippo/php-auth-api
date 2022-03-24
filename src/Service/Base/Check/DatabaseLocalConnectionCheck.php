<?php

namespace App\Service\Base\Check;

use Doctrine\DBAL\Connection;
use Hippy\Api\Service\Base\Check\AbstractDatabaseConnectionCheck;

class DatabaseLocalConnectionCheck extends AbstractDatabaseConnectionCheck
{
    /**
     * @param Connection $connection
     */
    public function __construct(Connection $connection)
    {
        parent::__construct('DB - local connection check', $connection);
    }
}
