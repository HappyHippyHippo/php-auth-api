<?php

namespace App\Config\Partial;

use Hippy\Config\Partial\AbstractPartial;

class Auth extends AbstractPartial
{
    protected const DOMAIN = 'auth';

    public function __construct()
    {
        parent::__construct(self::DOMAIN);
        $this->def = [
            'auth.tries' => 3,
            'auth.cool_down_ttl' => '+30 minutes',
            'auth.chap.enabled' => true,
            'auth.chap.challenge_ttl' => '+5 minutes',
            'auth.legacy.enabled' => true,
            'auth.token.ttl' => '+1 day',
            'auth.token.issuer' => 'hippy.com',
        ];
    }

    /**
     * @param array<string, mixed> $config
     * @return AbstractPartial
     */
    public function load(array $config = []): AbstractPartial
    {
        $this->loadType('auth.tries', 'int', $config);
        $this->loadType('auth.cool_down_ttl', 'string', $config);
        $this->loadType('auth.chap.enabled', 'bool', $config);
        $this->loadType('auth.chap.challenge_ttl', 'string', $config);
        $this->loadType('auth.legacy.enabled', 'bool', $config);
        $this->loadType('auth.token.ttl', 'string', $config);
        $this->loadType('auth.token.issuer', 'string', $config);

        return $this;
    }
}
