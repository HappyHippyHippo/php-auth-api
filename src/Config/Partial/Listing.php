<?php

namespace App\Config\Partial;

use Hippy\Config\Partial\AbstractPartial;

class Listing extends AbstractPartial
{
    protected const DOMAIN = 'listing';

    public function __construct()
    {
        parent::__construct(self::DOMAIN);
        $this->def = [
            'listing.max' => 50,
        ];
    }

    /**
     * @param array<string, mixed> $config
     * @return AbstractPartial
     */
    public function load(array $config = []): AbstractPartial
    {
        $this->loadType('listing.max', 'int', $config);

        return $this;
    }
}
