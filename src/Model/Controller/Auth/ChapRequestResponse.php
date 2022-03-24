<?php

namespace App\Model\Controller\Auth;

use DateTime;
use Hippy\Model\Model;

/**
 * @method string getChallenge()
 * @method ChapRequestResponse setChallenge(string $value)
 * @method string getChallengeSalt()
 * @method ChapRequestResponse setChallengeSalt(string $value)
 * @method string getPasswordSalt()
 * @method ChapRequestResponse setPasswordSalt(string $value)
 * @method DateTime getTtl()
 * @method ChapRequestResponse setTtl(DateTime $value)
 */
class ChapRequestResponse extends Model
{
    /**
     * @param string $challenge
     * @param string $challengeSalt
     * @param string $passwordSalt
     * @param DateTime $ttl
     */
    public function __construct(
        protected string $challenge,
        protected string $challengeSalt,
        protected string $passwordSalt,
        protected DateTime $ttl,
    ) {
        parent::__construct();

        $this->addParser('ttl', function (DateTime $date) {
            return $this->fromDateTime($date);
        });
    }
}
