<?php

namespace App\Tests\Flow\Auth;

use App\Model\Entity\Local\Challenge;
use App\Model\Entity\Local\Token;
use App\Tests\Flow\EndpointTester;
use App\Tests\LocalFixtures;
use DateTime;
use Exception;
use Symfony\Component\HttpFoundation\Response;

class TokenRecoverTest extends EndpointTester
{
    /** @var string */
    private const JWT = '__dummy_jwt__';

    /** @var string */
    private const RECOVER = '__dummy_recover__';

    /**
     * @return void
     */
    public function testTokenNotFound(): void
    {
        $expectedBody = [
            'status' => [
                'success' => false,
                'errors' => [['code' => 's2.e101.c17', 'message' => 'invalid recover']],
            ],
        ];

        $this->client->request('PUT', '/auth', ['jwt' => self::JWT, 'recover' => self::RECOVER]);
        $response = $this->client->getResponse();
        $responseBody = json_decode((string) $response->getContent(), true);

        $this->assertEquals(Response::HTTP_UNAUTHORIZED, $response->getStatusCode());
        $this->assertEquals($expectedBody, $responseBody);

        $this->assertLogFailure(
            'auth.token.recover',
            Response::HTTP_UNAUTHORIZED,
            $expectedBody,
            [[$this, 'assertLogRequestValues']]
        );
    }

    /**
     * @return void
     * @throws Exception
     */
    public function testInvalidRecoverForSelectedToken(): void
    {
        $fixtures = new LocalFixtures();
        $user = $fixtures->addUser();
        $fixtures->addToken(['user' => $user, 'recover' => '__dummy_other_recover__']);
        $this->loadFixtures($fixtures);

        $expectedBody = [
            'status' => [
                'success' => false,
                'errors' => [['code' => 's2.e101.c17', 'message' => 'invalid recover']],
            ],
        ];

        $this->client->request('PUT', '/auth', ['jwt' => self::JWT, 'recover' => self::RECOVER]);
        $response = $this->client->getResponse();
        $responseBody = json_decode((string) $response->getContent(), true);

        $this->assertEquals(Response::HTTP_UNAUTHORIZED, $response->getStatusCode());
        $this->assertEquals($expectedBody, $responseBody);

        $this->assertLogFailure(
            'auth.token.recover',
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
        $user = $fixtures->addUser(['deletedAt' => new DateTime()]);
        $fixtures->addToken(['user' => $user]);
        $this->loadFixtures($fixtures);

        $expectedBody = [
            'status' => [
                'success' => false,
                'errors' => [['code' => 's2.e101.c11', 'message' => 'user not active']],
            ],
        ];

        $this->client->request('PUT', '/auth', ['jwt' => self::JWT, 'recover' => self::RECOVER]);
        $response = $this->client->getResponse();
        $responseBody = json_decode((string) $response->getContent(), true);

        $this->assertEquals(Response::HTTP_UNAUTHORIZED, $response->getStatusCode());
        $this->assertEquals($expectedBody, $responseBody);

        $this->assertLogFailure(
            'auth.token.recover',
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
        $user = $fixtures->addUser(['enabled' => false]);
        $fixtures->addToken(['user' => $user]);
        $this->loadFixtures($fixtures);

        $expectedBody = [
            'status' => [
                'success' => false,
                'errors' => [['code' => 's2.e101.c11', 'message' => 'user not active']],
            ],
        ];

        $this->client->request('PUT', '/auth', ['jwt' => self::JWT, 'recover' => self::RECOVER]);
        $response = $this->client->getResponse();
        $responseBody = json_decode((string) $response->getContent(), true);

        $this->assertEquals(Response::HTTP_UNAUTHORIZED, $response->getStatusCode());
        $this->assertEquals($expectedBody, $responseBody);

        $this->assertLogFailure(
            'auth.token.recover',
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
        $user = $fixtures->addUser(['coolDown' => new DateTime('+1 day')]);
        $fixtures->addToken(['user' => $user]);
        $this->loadFixtures($fixtures);

        $expectedBody = [
            'status' => [
                'success' => false,
                'errors' => [['code' => 's2.e101.c14', 'message' => 'user in cool down']],
            ],
        ];

        $this->client->request('PUT', '/auth', ['jwt' => self::JWT, 'recover' => self::RECOVER]);
        $response = $this->client->getResponse();
        $responseBody = json_decode((string) $response->getContent(), true);

        $this->assertEquals(Response::HTTP_UNAUTHORIZED, $response->getStatusCode());
        $this->assertEquals($expectedBody, $responseBody);

        $this->assertLogFailure(
            'auth.token.recover',
            Response::HTTP_UNAUTHORIZED,
            $expectedBody,
            [[$this, 'assertLogRequestValues']]
        );
    }

    /**
     * @return void
     * @throws Exception
     */
    public function testNotLastTokenOfUser(): void
    {
        $fixtures = new LocalFixtures();
        $user = $fixtures->addUser();
        $fixtures->addToken(['user' => $user, 'createdAt' => new DateTime('-1 day')]);
        $fixtures->addToken([
            'user' => $user,
            'jwt' => self::JWT . '1',
            'recover' => self::RECOVER . '1',
            'createdAt' => new DateTime('+1 day'),
        ]);
        $this->loadFixtures($fixtures);

        $expectedBody = [
            'status' => [
                'success' => false,
                'errors' => [['code' => 's2.e101.c18', 'message' => 'invalid last token']],
            ],
        ];

        $this->client->request('PUT', '/auth', ['jwt' => self::JWT, 'recover' => self::RECOVER]);
        $response = $this->client->getResponse();
        $responseBody = json_decode((string) $response->getContent(), true);

        $this->assertEquals(Response::HTTP_UNAUTHORIZED, $response->getStatusCode());
        $this->assertEquals($expectedBody, $responseBody);

        $this->assertLogFailure(
            'auth.token.recover',
            Response::HTTP_UNAUTHORIZED,
            $expectedBody,
            [[$this, 'assertLogRequestValues']]
        );
    }

    /**
     * @return void
     * @throws Exception
     */
    public function testStillActiveToken(): void
    {
        $fixtures = new LocalFixtures();
        $user = $fixtures->addUser();
        $fixtures->addToken(['user' => $user]);
        $this->loadFixtures($fixtures);

        $expectedBody = [
            'status' => [
                'success' => false,
                'errors' => [['code' => 's2.e101.c19', 'message' => 'token still active']],
            ],
        ];

        $this->client->request('PUT', '/auth', ['jwt' => self::JWT, 'recover' => self::RECOVER]);
        $response = $this->client->getResponse();
        $responseBody = json_decode((string) $response->getContent(), true);

        $this->assertEquals(Response::HTTP_UNAUTHORIZED, $response->getStatusCode());
        $this->assertEquals($expectedBody, $responseBody);

        $this->assertLogFailure(
            'auth.token.recover',
            Response::HTTP_UNAUTHORIZED,
            $expectedBody,
            [[$this, 'assertLogRequestValues']]
        );
    }

    /**
     * @return void
     * @throws Exception
     */
    public function testRegenerateToken(): void
    {
        $fixtures = new LocalFixtures();
        $user = $fixtures->addUser();
        $token = $fixtures->addToken(['user' => $user, 'ttl' => new DateTime('-1 day')]);
        $fixtures->addChallenge(['user' => $user, 'token' => $token]);
        $this->loadFixtures($fixtures);

        $this->client->request('PUT', '/auth', ['jwt' => self::JWT, 'recover' => self::RECOVER]);
        $response = $this->client->getResponse();
        $responseBody = json_decode((string) $response->getContent(), true);

        $this->assertEquals(Response::HTTP_CREATED, $response->getStatusCode());

        $tokens = $this->localManager->getRepository(Token::class)->findAll();
        $this->assertCount(2, $tokens);
        $this->assertEquals($responseBody['data']['jwt'], $tokens[1]->getJwt());
        $this->assertEquals($responseBody['data']['recover'], $tokens[1]->getRecover());

        $challenges = $this->localManager->getRepository(Challenge::class)->findAll();
        $this->assertCount(2, $challenges);
        $this->assertEquals(new DateTime('1900-01-01'), $challenges[1]->getTtl());
        $this->assertEquals($tokens[1]->getId(), $challenges[1]->getToken()?->getId());
        $this->assertEquals($user->getId(), $challenges[1]->getUser()?->getId());
        $this->assertEquals('recover/1', $challenges[1]->getMessage());

        $this->assertLogSuccess(
            'auth.token.recover',
            Response::HTTP_CREATED,
            [[$this, 'assertLogRequestValues']]
        );
    }

    /**
     * @param array<string, mixed> $records
     * @return void
     */
    public function assertLogRequestValues(array $records): void
    {
        foreach ($records as $record) {
            $this->assertLogRequestBody($record, ['jwt' => self::JWT, 'recover' => self::RECOVER]);
        }
    }
}
