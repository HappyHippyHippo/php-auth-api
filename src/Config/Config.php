<?php

namespace App\Config;

use DateTime;
use Hippy\Api\Config\ApiConfig;
use TypeError;

class Config extends ApiConfig
{
    /**
     * @return int
     * @throws TypeError
     */
    public function getListingMaxRecords(): int
    {
        return $this->int('listing.max');
    }

    /**
     * @return int
     * @throws TypeError
     */
    public function getAuthTries(): int
    {
        return $this->int('auth.tries');
    }

    /**
     * @return DateTime
     * @throws TypeError
     */
    public function getAuthCoolDownTTL(): DateTime
    {
        return $this->datetime('auth.cool_down_ttl');
    }

    /**
     * @return bool
     * @throws TypeError
     */
    public function isAuthChapEnabled(): bool
    {
        return $this->bool('auth.chap.enabled');
    }

    /**
     * @return DateTime
     * @throws TypeError
     */
    public function getAuthChapChallengeTTL(): DateTime
    {
        return $this->datetime('auth.chap.challenge_ttl');
    }

    /**
     * @return bool
     * @throws TypeError
     */
    public function isAuthLegacyEnabled(): bool
    {
        return $this->bool('auth.legacy.enabled');
    }

    /**
     * @return DateTime
     * @throws TypeError
     */
    public function getAuthTokenTTL(): DateTime
    {
        return $this->datetime('auth.token.ttl');
    }

    /**
     * @return string
     * @throws TypeError
     */
    public function getAuthTokenIssuer(): string
    {
        return $this->string('auth.token.issuer');
    }
}
