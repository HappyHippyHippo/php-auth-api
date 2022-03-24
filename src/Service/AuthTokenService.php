<?php

namespace App\Service;

use App\Config\Config;
use App\Error\ErrorCode;
use App\Model\Controller\Auth\CheckRequest;
use App\Model\Controller\Auth\RecoverRequest;
use App\Model\Controller\Auth\TokenResponse;
use App\Model\Entity\Local\Challenge;
use App\Model\Entity\Local\Token;
use App\Model\Entity\Local\User;
use App\Repository\Local\TokenRepository;
use App\Repository\RepositoryFactory;
use DateTime;
use Doctrine\ORM\NonUniqueResultException;
use Symfony\Component\HttpFoundation\Response;

class AuthTokenService extends AbstractService
{
    /** @var string */
    public const ID_PREFIX = 'recover/';

    /**
     * @param Config $config
     * @param RepositoryFactory $repoFactory
     * @param TokenGeneratorService $tokenGenerator
     */
    public function __construct(
        protected Config $config,
        protected RepositoryFactory $repoFactory,
        protected TokenGeneratorService $tokenGenerator,
    ) {
    }

    /**
     * @param CheckRequest $request
     * @return void
     */
    public function check(CheckRequest $request): void
    {
        // get token information form the persistence layer
        $tokenRepo = $this->repoFactory->getTokens();
        $token = $tokenRepo->findByJwt($request->getJwt());
        if (is_null($token)) {
            $this->throws(Response::HTTP_NOT_FOUND, ErrorCode::TOKEN_NOT_FOUND);
        }

        // check if the token is valid
        if ($token->getTtl() < new DateTime()) {
            $this->throws(Response::HTTP_GONE, ErrorCode::TOKEN_EXPIRED);
        }
    }

    /**
     * @param RecoverRequest $request
     * @return TokenResponse
     * @throws NonUniqueResultException
     */
    public function recover(RecoverRequest $request): TokenResponse
    {
        // get requested token
        $tokenRepo = $this->repoFactory->getTokens();
        $token = $tokenRepo->findByJwt($request->getJwt());
        if (is_null($token) || $request->getRecover() != $token->getRecover()) {
            $this->throws(Response::HTTP_UNAUTHORIZED, ErrorCode::INVALID_RECOVER);
        }

        // get token user and validate user
        $user = $this->getUser($token);

        // check if the requested token is the last user token
        $this->checkUserLastToken($tokenRepo, $user, $token);

        // generate and store the challenge
        $now = new DateTime();
        $challenge = new Challenge([
            'user' => $user,
            'valid' => true,
            'message' => self::ID_PREFIX . $token->getId(),
            'ttl' => new DateTime('1900-01-01 00:00:00'),
            'createdAt' => $now,
            'createdBy' => $user,
            'updatedAt' => $now,
            'updatedBy' => $user,
        ]);
        $challengeRepo = $this->repoFactory->getChallenges();
        $challengeRepo->persist($challenge);

        // generate a new token
        $token = $this->tokenGenerator->generateToken($user, (int) $challenge->getId());

        // associate the challenge to the generated token
        $challengeRepo->update($challenge->setToken($token));

        // send the generated token information to the client
        return new TokenResponse((string) $token->getJwt(), (string) $token->getRecover());
    }

    /**
     * @param Token $token
     * @return User
     */
    private function getUser(Token $token): User
    {
        $user = $this->repoFactory->getUsers()->findById((int) $token->getUser()?->getId());
        if (is_null($user) || !$user->isEnabled()) {
            $this->throws(Response::HTTP_UNAUTHORIZED, ErrorCode::USER_NOT_ACTIVE);
        }
        if ($user->getCoolDown() > new DateTime()) {
            $this->throws(Response::HTTP_UNAUTHORIZED, ErrorCode::USER_IN_COOL_DOWN);
        }

        return $user;
    }

    /**
     * @param TokenRepository $tokenRepo
     * @param User $user
     * @param Token $token
     * @return void
     * @throws NonUniqueResultException
     */
    private function checkUserLastToken(TokenRepository $tokenRepo, User $user, Token $token)
    {
        $lastToken = $tokenRepo->findLastOfUser($user);
        if (is_null($lastToken) || $lastToken->getId() != $token->getId()) {
            $this->throws(Response::HTTP_UNAUTHORIZED, ErrorCode::INVALID_LAST_TOKEN);
        }
        if ($lastToken->getTtl() > new DateTime()) {
            $this->throws(Response::HTTP_UNAUTHORIZED, ErrorCode::TOKEN_STILL_ACTIVE);
        }
    }
}
