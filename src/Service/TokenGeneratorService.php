<?php

namespace App\Service;

use App\Config\Config;
use App\Model\Entity\Local\Token;
use App\Model\Entity\Local\User;
use DateTime;
use Firebase\JWT\JWT;

class TokenGeneratorService
{
    /**
     * @param Config $config
     * @param HasherService $hasher
     */
    public function __construct(
        protected Config $config,
        protected HasherService $hasher
    ) {
    }

    /**
     * @param User $user
     * @param int $challengeId
     * @param int $stringLength
     * @return Token
     */
    public function generateToken(
        User $user,
        int $challengeId,
        int $stringLength = HasherService::DEFAULT_STRING_LENGTH
    ): Token {
        // generate the token generation strings
        $secret = $this->hasher->string($stringLength);
        $recover = $this->hasher->string($stringLength);
        $ttl = $this->config->getAuthTokenTTL();

        // generate the token JWT
        $jwt = $this->generateJWT($this->config->getAuthTokenIssuer(), $user, $challengeId, $secret, $ttl);
        $now = new DateTime();

        // return the token record
        return new Token([
            'user' => $user,
            'secret' => $secret,
            'recover' => $recover,
            'ttl' => $ttl,
            'jwt' => $jwt,
            'createdAt' => $now,
            'updatedAt' => $now,
        ]);
    }

    /**
     * @param string $challenge
     * @param string $salt
     * @param string $password
     * @return string
     */
    public function generateResponse(
        string $challenge,
        string $salt,
        string $password
    ): string {
        $combined = '';
        for ($i = 0; $i < strlen($challenge); ++$i) {
            $combined .= $challenge[$i] . $salt[$i] . $password[$i];
        }
        return $this->hasher->hash($combined);
    }

    /**
     * @param string $issuer
     * @param User $user
     * @param int $id
     * @param string $secret
     * @param DateTime $ttl
     * @return string
     */
    protected function generateJWT(
        string $issuer,
        User $user,
        int $id,
        string $secret,
        DateTime $ttl
    ): string {
        $content = [
            'iss' => $issuer,
            'sub' => $user->getId(),
            'exp' => $ttl->getTimestamp(),
            'nbf' => strtotime('now'),
            'iat' => strtotime('now'),
            'jti' => $id,

            'email' => $user->getEmail(),
        ];

        return JWT::encode($content, $secret, 'HS256');
    }
}
