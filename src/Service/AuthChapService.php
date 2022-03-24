<?php

namespace App\Service;

use App\Config\Config;
use App\Error\ErrorCode;
use App\Model\Controller\Auth\ChapRequestRequest;
use App\Model\Controller\Auth\ChapRequestResponse;
use App\Model\Controller\Auth\ChapResponseRequest;
use App\Model\Controller\Auth\TokenResponse;
use App\Model\Entity\Local\Challenge;
use App\Model\Entity\Local\User;
use App\Repository\RepositoryFactory;
use DateTime;
use Doctrine\ORM\NonUniqueResultException;
use Symfony\Component\HttpFoundation\Response;

class AuthChapService extends AbstractService
{
    /** @var string */
    public const DEFAULT_MESSAGE = 'local/';

    /**
     * @param Config $config
     * @param RepositoryFactory $repoFactory
     * @param TokenGeneratorService $tokenGenerator
     * @param HasherService $hasher
     */
    public function __construct(
        protected Config $config,
        protected RepositoryFactory $repoFactory,
        protected TokenGeneratorService $tokenGenerator,
        protected HasherService $hasher,
    ) {
    }

    /**
     * @param ChapRequestRequest $request
     * @return ChapRequestResponse
     * @throws NonUniqueResultException
     */
    public function request(ChapRequestRequest $request): ChapRequestResponse
    {
        // retrieve the requesting user
        $userRepo = $this->repoFactory->getUsers();
        $user = $this->validateUser($userRepo->findByEmail($request->getEmail()));

        // if a challenge already exists, return the founded challenge
        $challengeRepo = $this->repoFactory->getChallenges();
        $challenge = $challengeRepo->findOfUser($user);
        if (!is_null($challenge)) {
            return new ChapRequestResponse(
                (string) $challenge->getChallenge(),
                (string) $challenge->getSalt(),
                (string) $user->getSalt(),
                $challenge->getTtl() ?? new DateTime('1900-01-01 00:00:00')
            );
        }

        // generate and store the local challenge
        $now = new DateTime();
        $challenge = new Challenge([
            'user' => $user,
            'challenge' => $this->hasher->string(),
            'salt' => $this->hasher->string(),
            'message' => self::DEFAULT_MESSAGE,
            'tries' => $this->config->getAuthTries(),
            'ttl' => $this->config->getAuthChapChallengeTTL(),
            'createdAt' => $now,
            'updatedAt' => $now,
        ]);
        $challengeRepo->persist($challenge);

        // send challenge request response
        return new ChapRequestResponse(
            (string) $challenge->getChallenge(),
            (string) $challenge->getSalt(),
            (string) $user->getSalt(),
            $challenge->getTtl() ?? new DateTime('1900-01-01 00:00:00')
        );
    }

    /**
     * @param ChapResponseRequest $request
     * @return TokenResponse
     * @throws NonUniqueResultException
     */
    public function response(ChapResponseRequest $request): TokenResponse
    {
        // retrieve the requesting user
        $userRepo = $this->repoFactory->getUsers();
        $user = $this->validateUser($userRepo->findByEmail($request->getEmail()));

        // if a challenge already exists for the user and invalidate if is not the correct challenge
        $challengeRepo = $this->repoFactory->getChallenges();
        $challenge = $challengeRepo->findOfUser($user);
        if (is_null($challenge)) {
            $this->throws(Response::HTTP_UNAUTHORIZED, ErrorCode::INVALID_CHALLENGE);
        }
        $challengeString = (string) $challenge->getChallenge();
        if ($challengeString != $request->getChallenge()) {
            $this->throws(Response::HTTP_UNAUTHORIZED, ErrorCode::INVALID_CHALLENGE);
        }

        // validate the challenge response information
        $response = $this->tokenGenerator->generateResponse(
            $challengeString,
            (string) $challenge->getSalt(),
            (string) $user->getPassword()
        );
        if ($request->getResponse() != $response) {
            // invalidate the challenge
            $challengeRepo->persist($challenge->disable());

            // decrease the number of tries on the user
            $user->decrementTries();
            if (0 == $user->getTries()) {
                $user->setCoolDown($this->config->getAuthCoolDownTTL());
            }
            $userRepo->update($user);

            $this->throws(Response::HTTP_UNAUTHORIZED, ErrorCode::INVALID_RESPONSE);
        }

        // re-instate the number of authentication tries
        $userRepo->update($user->setTries($this->config->getAuthTries()));

        // re-use any existing token
        $tokenRepo = $this->repoFactory->getTokens();
        $token = $tokenRepo->findActiveOfUser($user);
        if (!is_null($token)) {
            // 'terminate' the challenge and associate it to the founded token
            $challengeRepo->update($challenge->setToken($token));
            return new TokenResponse((string) $token->getJwt(), (string) $token->getRecover());
        }

        // generate authentication token
        $token = $this->tokenGenerator->generateToken($user, (int) $challenge->getId());
        $tokenRepo->persist($token);

        // 'terminate' the challenge and associate it to the generated token
        $challengeRepo->update($challenge->setToken($token));
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
