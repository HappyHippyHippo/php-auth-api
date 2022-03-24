<?php

namespace App\Config\Partial;

use Hippy\Config\Partial\AbstractPartial;

class DatabaseLocal extends AbstractPartial
{
    protected const DOMAIN = 'database.local';

    public function __construct()
    {
        parent::__construct(self::DOMAIN);
        $this->def = [
            'database.local.driver' => 'pdo_mysql',
            'database.local.host' => '',
            'database.local.port' => 3306,
            'database.local.version' => '',
            'database.local.user' => '',
            'database.local.password' => '',
            'database.local.schema' => '',
        ];
    }

    /**
     * @param array<string, mixed> $config
     * @return AbstractPartial
     */
    public function load(array $config = []): AbstractPartial
    {
        $this->loadType('database.local.driver', 'string', $config);
        $this->loadType('database.local.host', 'string', $config);
        $this->loadType('database.local.port', 'int', $config);
        $this->loadType('database.local.version', 'string', $config);
        $this->loadType('database.local.user', 'string', $config);
        $this->loadType('database.local.password', 'string', $config);
        $this->loadType('database.local.schema', 'string', $config);

        return $this;
    }
}
