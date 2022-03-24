<?php

namespace App\Service;

use App\Config\Config;
use App\Error\ErrorCode;
use App\Model\Controller\Auth\LegacyRequest;
use App\Model\Controller\Auth\TokenResponse;
use App\Model\Entity\Local\Challenge;
use App\Model\Entity\Local\User;
use App\Repository\RepositoryFactory;
use DateTime;
use Doctrine\ORM\NonUniqueResultException;
use Symfony\Component\HttpFoundation\Response;

class AuthLegacyService extends AbstractService
{
    /** @var string */
    public const DEFAULT_MESSAGE = 'legacy';

    /**
     * @param Config $config
     * @param RepositoryFactory $repoFactory
     * @param TokenGeneratorService $tokenGenerator
     * @param HasherService $hasher.
     */
    public function __construct(
        protected Config $config,
        protected RepositoryFactory $repoFactory,
        protected TokenGeneratorService $tokenGenerator,
        protected HasherService $hasher,
    ) {
    }

    /**
     * @param LegacyRequest $request
     * @return TokenResponse
     * @throws NonUniqueResultException
     */
    public function authenticate(LegacyRequest $request): TokenResponse
    {
        $userRepo = $this->repoFactory->getUsers();

        // get authentication user
        $user = $this->validateUser($userRepo->findByEmail($request->getEmail()));

        // hash the received password
        $hash = $this->hasher->hash($request->getPassword(), (string) $user->getSalt());
        if ($user->getPassword() != $hash) {
            // decrease the number of tries on the user
            $user->decrementTries();
            if (0 == $user->getTries()) {
                $user->setCoolDown($this->config->getAuthCoolDownTTL());
            }
            $userRepo->update($user);

            $this->throws(Response::HTTP_UNAUTHORIZED, ErrorCode::USER_INVALID_AUTH);
        }

        // re-instate the number of authentication tries
        $userRepo->update($user->setTries($this->config->getAuthTries()));

        // return any existing user token
        $tokenRepo = $this->repoFactory->getTokens();
        $token = $tokenRepo->findActiveOfUser($user);
        if (!is_null($token)) {
            return new TokenResponse((string) $token->getJwt(), (string) $token->getRecover());
        }

        // generate and store the legacy challenge
        $now = new DateTime();
        $challenge = new Challenge([
            'user' => $user,
            'message' => self::DEFAULT_MESSAGE,
            'ttl' => new DateTime('1900-01-01 00:00:00'),
            'createdAt' => $now,
            'createdBy' => $user,
            'updatedAt' => $now,
            'updatedBy' => $user,
        ]);
        $challengeRepo = $this->repoFactory->getChallenges();
        $challengeRepo->persist($challenge);

        // generate authentication token
        $token = $this->tokenGenerator->generateToken($user, (int) $challenge->getId());
        $tokenRepo->persist($token);

        // associate the challenge to the generated token
        $challengeRepo->update($challenge->setToken($token));

        // send the generated token information to the client
        return new TokenResponse((string) $token->getJwt(), (string) $token->getRecover());
    }

    /**
     * @param User|null $user
     * @return User
     */
    protected function validateUser(?User $user): User
    {
        if (is_null($user) || !$user->isEnabled()) {
            $this->throws(Response::HTTP_UNAUTHORIZED, ErrorCode::USER_NOT_ACTIVE);
        }
        if (empty($user->getPassword()) || empty($user->getSalt())) {
            $this->throws(Response::HTTP_UNAUTHORIZED, ErrorCode::USER_MISSING_AUTH);
        }
        if ($user->getCoolDown() > new DateTime()) {
            $this->throws(Response::HTTP_UNAUTHORIZED, ErrorCode::USER_IN_COOL_DOWN);
        }

        return $user;
    }
}
