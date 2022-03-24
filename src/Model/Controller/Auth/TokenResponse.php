<?php

namespace App\Model\Controller\Auth;

use Hippy\Model\Model;

/**
 * @method string getJwt()
 * @method TokenResponse setJwt(string $value)
 * @method string getRecover()
 * @method TokenResponse setRecover(string $value)
 */
class TokenResponse extends Model
{
    /**
     * @param string $jwt
     * @param string $recover
     */
    public function __construct(
        protected string $jwt,
        protected string $recover,
    ) {
        parent::__construct();
    }
}
