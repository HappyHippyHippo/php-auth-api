<?php

namespace App\Tests\Flow\Auth;

use App\Controller\Auth\AuthLegacyController;
use App\Model\Entity\Local\Challenge;
use App\Model\Entity\Local\Token;
use App\Model\Entity\Local\User;
use App\Tests\Flow\EndpointTester;
use App\Tests\LocalFixtures;
use DateTime;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AuthLegacyTest extends EndpointTester
{
    /** @var string */
    private const EMAIL = 'email@email.com';

    /** @var string */
    private const PASSWORD =
        'b84da2599c0366caa309b65ba96042dab9e9f78341df3bafbcd92d97882f3319b' .
        '2c034f1abc7f3b35bc24ae42532d55b7669cc97ea73d8a994ca649fe05374ff';

    /**
     * @return void
     */
    public function testDisabled(): void
    {
        $expectedBody = [
            'status' => [
                'success' => false,
                'errors' => [['code' => 's2.e104.c2', 'message' => 'Not enabled']],
            ],
        ];

        putenv('HIPPY_AUTH_LEGACY_ENABLED=false');
        $this->client->request('POST', '/auth/legacy', ['email' => self::EMAIL, 'password' => '__dummy_pass__']);
        putenv('HIPPY_AUTH_LEGACY_ENABLED=');
        $response = $this->client->getResponse();
        $responseBody = json_decode((string) $response->getContent(), true);

        $this->assertEquals(Response::HTTP_SERVICE_UNAVAILABLE, $response->getStatusCode());
        $this->assertEquals($expectedBody, $responseBody);

        $this->assertLogFailure(
            'auth.legacy',
            Response::HTTP_SERVICE_UNAVAILABLE,
            $expectedBody,
            [[$this, 'assertLogRequestValues']]
        );
    }

    /**
     * @return void
     */
    public function testUserNotFound(): void
    {
        $expectedBody = [
            'status' => [
                'success' => false,
                'errors' => [['code' => 's2.e104.c11', 'message' => 'user not active']],
            ],
        ];

        $this->client->request('POST', '/auth/legacy', ['email' => self::EMAIL, 'password' => '__dummy_pass__']);
        $response = $this->client->getResponse();
        $responseBody = json_decode((string) $response->getContent(), true);

        $this->assertEquals(Response::HTTP_UNAUTHORIZED, $response->getStatusCode());
        $this->assertEquals($expectedBody, $responseBody);

        $this->assertLogFailure(
            'auth.legacy',
            Response::HTTP_UNAUTHORIZED,
            $expectedBody,
            [[$this, 'assertLogRequestValues']]
        );
    }

    /**
     * @return void
     * @throws Exception
     */
    public function testUserDeleted(): void
    {
        $fixtures = new LocalFixtures();
        $fixtures->addUser(['email' => self::EMAIL, 'deletedAt' => new DateTime()]);
        $this->loadFixtures($fixtures);

        $expectedBody = [
            'status' => [
                'success' => false,
                'errors' => [['code' => 's2.e104.c11', 'message' => 'user not active']],
            ],
        ];

        $this->client->request('POST', '/auth/legacy', ['email' => self::EMAIL, 'password' => '__dummy_pass__']);
        $response = $this->client->getResponse();
        $responseBody = json_decode((string) $response->getContent(), true);

        $this->assertEquals(Response::HTTP_UNAUTHORIZED, $response->getStatusCode());
        $this->assertEquals($expectedBody, $responseBody);

        $this->assertLogFailure(
            'auth.legacy',
            Response::HTTP_UNAUTHORIZED,
            $expectedBody,
            [[$this, 'assertLogRequestValues']]
        );
    }

    /**
     * @return void
     * @throws Exception
     */
    public function testUserNotActive(): void
    {
        $fixtures = new LocalFixtures();
        $fixtures->addUser(['email' => self::EMAIL, 'enabled' => false]);
        $this->loadFixtures($fixtures);

        $expectedBody = [
            'status' => [
                'success' => false,
                'errors' => [['code' => 's2.e104.c11', 'message' => 'user not active']],
            ],
        ];

        $this->client->request('POST', '/auth/legacy', ['email' => self::EMAIL, 'password' => '__dummy_pass__']);
        $response = $this->client->getResponse();
        $responseBody = json_decode((string) $response->getContent(), true);

        $this->assertEquals(Response::HTTP_UNAUTHORIZED, $response->getStatusCode());
        $this->assertEquals($expectedBody, $responseBody);

        $this->assertLogFailure(
            'auth.legacy',
            Response::HTTP_UNAUTHORIZED,
            $expectedBody,
            [[$this, 'assertLogRequestValues']]
        );
    }

    /**
     * @return void
     * @throws Exception
     */
    public function testMissingUserPassword(): void
    {
        $fixtures = new LocalFixtures();
        $fixtures->addUser(['email' => self::EMAIL, 'password' => '']);
        $this->loadFixtures($fixtures);

        $expectedBody = [
            'status' => [
                'success' => false,
                'errors' => [['code' => 's2.e104.c12', 'message' => 'user authentication missing']],
            ],
        ];

        $this->client->request('POST', '/auth/legacy', ['email' => self::EMAIL, 'password' => '__dummy_pass__']);
        $response = $this->client->getResponse();
        $responseBody = json_decode((string) $response->getContent(), true);

        $this->assertEquals(Response::HTTP_UNAUTHORIZED, $response->getStatusCode());
        $this->assertEquals($expectedBody, $responseBody);

        $this->assertLogFailure(
            'auth.legacy',
            Response::HTTP_UNAUTHORIZED,
            $expectedBody,
            [[$this, 'assertLogRequestValues']]
        );
    }

    /**
     * @return void
     * @throws Exception
     */
    public function testMissingUserPasswordSalt(): void
    {
        $fixtures = new LocalFixtures();
        $fixtures->addUser(['email' => self::EMAIL, 'salt' => '']);
        $this->loadFixtures($fixtures);

        $expectedBody = [
            'status' => [
                'success' => false,
                'errors' => [['code' => 's2.e104.c12', 'message' => 'user authentication missing']],
            ],
        ];

        $this->client->request('POST', '/auth/legacy', ['email' => self::EMAIL, 'password' => '__dummy_pass__']);
        $response = $this->client->getResponse();
        $responseBody = json_decode((string) $response->getContent(), true);

        $this->assertEquals(Response::HTTP_UNAUTHORIZED, $response->getStatusCode());
        $this->assertEquals($expectedBody, $responseBody);

        $this->assertLogFailure(
            'auth.legacy',
            Response::HTTP_UNAUTHORIZED,
            $expectedBody,
            [[$this, 'assertLogRequestValues']]
        );
    }

    /**
     * @return void
     * @throws Exception
     */
    public function testUserInCoolDown(): void
    {
        $fixtures = new LocalFixtures();
        $fixtures->addUser(['email' => self::EMAIL, 'coolDown' => new DateTime('+5 minutes')]);
        $this->loadFixtures($fixtures);

        $expectedBody = [
            'status' => [
                'success' => false,
                'errors' => [['code' => 's2.e104.c14', 'message' => 'user in cool down']],
            ],
        ];

        $this->client->request('POST', '/auth/legacy', ['email' => self::EMAIL, 'password' => '__dummy_pass__']);
        $response = $this->client->getResponse();
        $responseBody = json_decode((string) $response->getContent(), true);

        $this->assertEquals(Response::HTTP_UNAUTHORIZED, $response->getStatusCode());
        $this->assertEquals($expectedBody, $responseBody);

        $this->assertLogFailure(
            'auth.legacy',
            Response::HTTP_UNAUTHORIZED,
            $expectedBody,
            [[$this, 'assertLogRequestValues']]
        );
    }

    /**
     * @return void
     * @throws Exception
     */
    public function testInvalidPassword(): void
    {
        $fixtures = new LocalFixtures();
        $fixtures->addUser(['email' => self::EMAIL]);
        $this->loadFixtures($fixtures);

        $expectedBody = [
            'status' => [
                'success' => false,
                'errors' => [['code' => 's2.e104.c13', 'message' => 'invalid authentication information']],
            ],
        ];

        $this->client->request('POST', '/auth/legacy', ['email' => self::EMAIL, 'password' => '__dummy_pass__']);
        $response = $this->client->getResponse();
        $responseBody = json_decode((string) $response->getContent(), true);

        $this->assertEquals(Response::HTTP_UNAUTHORIZED, $response->getStatusCode());
        $this->assertEquals($expectedBody, $responseBody);

        $users = $this->localManager->getRepository(User::class)->findAll();
        $this->assertCount(1, $users);
        $this->assertEquals(2, $users[0]->getTries());

        $this->assertLogFailure(
            'auth.legacy',
            Response::HTTP_UNAUTHORIZED,
            $expectedBody,
            [[$this, 'assertLogRequestValues']]
        );
    }

    /**
     * @return void
     * @throws Exception
     */
    public function testSetUserInCoolDownOnFailAllTries(): void
    {
        $fixtures = new LocalFixtures();
        $fixtures->addUser(['email' => self::EMAIL, 'tries' => 0]);
        $this->loadFixtures($fixtures);

        $expectedBody = [
            'status' => [
                'success' => false,
                'errors' => [['code' => 's2.e104.c13', 'message' => 'invalid authentication information']],
            ],
        ];

        $this->client->request('POST', '/auth/legacy', ['email' => self::EMAIL, 'password' => '__dummy_pass__']);
        $response = $this->client->getResponse();
        $responseBody = json_decode((string) $response->getContent(), true);

        $this->assertEquals(Response::HTTP_UNAUTHORIZED, $response->getStatusCode());
        $this->assertEquals($expectedBody, $responseBody);

        $users = $this->localManager->getRepository(User::class)->findAll();
        $this->assertCount(1, $users);
        $this->assertEquals(0, $users[0]->getTries());
        $this->assertNotEquals(new DateTime('1900-01-01'), $users[0]->getCoolDown());

        $this->assertLogFailure(
            'auth.legacy',
            Response::HTTP_UNAUTHORIZED,
            $expectedBody,
            [[$this, 'assertLogRequestValues']]
        );
    }

    /**
     * @return void
     * @throws Exception
     */
    public function testSuccessfulAuthenticationReturnActiveTokens(): void
    {
        $fixtures = new LocalFixtures();
        $user = $fixtures->addUser(['tries' => 1, 'email' => self::EMAIL, 'password' => self::PASSWORD]);
        $token = $fixtures->addToken(['user' => $user]);
        $fixtures->addChallenge(['user' => $user, 'token' => $token]);
        $this->loadFixtures($fixtures);

        $this->client->request('POST', '/auth/legacy', ['email' => self::EMAIL, 'password' => '__dummy_password__']);
        $response = $this->client->getResponse();
        $responseBody = json_decode((string) $response->getContent(), true);

        $this->assertEquals(Response::HTTP_CREATED, $response->getStatusCode());

        $users = $this->localManager->getRepository(User::class)->findAll();
        $this->assertCount(1, $users);
        $this->assertEquals(3, $users[0]->getTries());

        $tokens = $this->localManager->getRepository(Token::class)->findAll();
        $this->assertCount(1, $tokens);
        $this->assertEquals($responseBody['data']['jwt'], $tokens[0]->getJwt());
        $this->assertEquals($responseBody['data']['recover'], $tokens[0]->getRecover());

        $challenges = $this->localManager->getRepository(Challenge::class)->findAll();
        $this->assertCount(1, $challenges);
        $this->assertEquals($user->getId(), $challenges[0]->getUser()?->getId());
        $this->assertEquals($token->getId(), $challenges[0]->getToken()?->getId());

        $this->assertLogSuccess(
            'auth.legacy',
            Response::HTTP_CREATED,
            [[$this, 'assertLogRequestValues']]
        );
    }

    /**
     * @return void
     * @throws Exception
     */
    public function testSuccessfulAuthenticationReturnGeneratedToken(): void
    {
        $fixtures = new LocalFixtures();
        $user = $fixtures->addUser(['tries' => 1, 'email' => self::EMAIL, 'password' => self::PASSWORD]);
        $token = $fixtures->addToken(['user' => $user, 'ttl' => new DateTime('-1 minute')]);
        $fixtures->addChallenge(['user' => $user, 'token' => $token]);
        $this->loadFixtures($fixtures);

        $this->client->request('POST', '/auth/legacy', ['email' => self::EMAIL, 'password' => '__dummy_password__']);
        $response = $this->client->getResponse();
        $responseBody = json_decode((string) $response->getContent(), true);

        $this->assertEquals(Response::HTTP_CREATED, $response->getStatusCode());

        $users = $this->localManager->getRepository(User::class)->findAll();
        $this->assertCount(1, $users);
        $this->assertEquals(3, $users[0]->getTries());

        $tokens = $this->localManager->getRepository(Token::class)->findAll();
        $this->assertCount(2, $tokens);
        $this->assertEquals($responseBody['data']['jwt'], $tokens[1]->getJwt());
        $this->assertEquals($responseBody['data']['recover'], $tokens[1]->getRecover());

        $challenges = $this->localManager->getRepository(Challenge::class)->findAll();
        $this->assertCount(2, $challenges);
        $this->assertEquals($user->getId(), $challenges[1]->getUser()?->getId());
        $this->assertEquals($tokens[1]->getId(), $challenges[1]->getToken()?->getId());
        $this->assertEquals("legacy", $challenges[1]->getMessage());

        $this->assertLogSuccess(
            'auth.legacy',
            Response::HTTP_CREATED,
            [[$this, 'assertLogRequestValues']]
        );
    }

    /**
     * @param array<int, mixed> $records
     * @return void
     */
    public function assertLogRequestValues(array $records)
    {
        foreach ($records as $record) {
            $this->assertLogRequestBody($record, ['email' => self::EMAIL, 'password' => '******']);
        }
    }
}
