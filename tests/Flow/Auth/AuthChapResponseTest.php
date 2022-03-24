<?php

namespace App\Tests\Flow\Auth;

use App\Model\Entity\Local\Challenge;
use App\Model\Entity\Local\Token;
use App\Model\Entity\Local\User;
use App\Tests\Flow\EndpointTester;
use App\Tests\LocalFixtures;
use DateTime;
use Exception;
use Symfony\Component\HttpFoundation\Response;

class AuthChapResponseTest extends EndpointTester
{
    /** @var string */
    private const EMAIL = 'email@email.com';

    /** @var string */
    private const CHALLENGE =
        'd000f48ee3e8626e9a559dd48da75bb8923be1457d1bce68a656a0588bbb47dd3' .
        'cecee65d5564c1c6467a8a8a6679baa9753d3b715d228692d0a59d8799b829a';

    /** @var string */
    private const RESPONSE =
        '62803356644ac63f676fae6f46bcb0a1a05b86da9c45b138599fdbb71034982f5' .
        'b1c62c058954cff2db8eac1d3fa8d3ddff776586e42bae329b8c56be97dd827';

    /**
     * @return void
     */
    public function testDisabled(): void
    {
        $expectedBody = [
            'status' => [
                'success' => false,
                'errors' => [['code' => 's2.e103.c2', 'message' => 'Not enabled']],
            ],
        ];

        putenv('HIPPY_AUTH_CHAP_ENABLED=false');
        $this->client->request(
            'POST',
            '/auth/chap',
            ['email' => self::EMAIL, 'challenge' => self::CHALLENGE, 'response' => self::RESPONSE]
        );
        putenv('HIPPY_AUTH_CHAP_ENABLED=');
        $response = $this->client->getResponse();
        $responseBody = json_decode((string) $response->getContent(), true);

        $this->assertEquals(Response::HTTP_SERVICE_UNAVAILABLE, $response->getStatusCode());
        $this->assertEquals($expectedBody, $responseBody);

        $this->assertLogFailure(
            'auth.chap.response',
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
                'errors' => [['code' => 's2.e103.c11', 'message' => 'user not active']],
            ],
        ];

        $this->client->request(
            'POST',
            '/auth/chap',
            ['email' => self::EMAIL, 'challenge' => self::CHALLENGE, 'response' => self::RESPONSE]
        );
        $response = $this->client->getResponse();
        $responseBody = json_decode((string) $response->getContent(), true);

        $this->assertEquals(Response::HTTP_UNAUTHORIZED, $response->getStatusCode());
        $this->assertEquals($expectedBody, $responseBody);

        $this->assertLogFailure(
            'auth.chap.response',
            Response::HTTP_UNAUTHORIZED,
            $expectedBody,
            [[$this, 'assertLogRequestValidValues']]
        );
    }

    /**
     * @return void
     * @throws Exception
     */
    public function testUserDeleted(): void
    {
        $fixtures = new LocalFixtures();
        $fixtures->addUser(['deletedAt' => new DateTime()]);
        $this->loadFixtures($fixtures);

        $expectedBody = [
            'status' => [
                'success' => false,
                'errors' => [['code' => 's2.e103.c11', 'message' => 'user not active']],
            ],
        ];

        $this->client->request(
            'POST',
            '/auth/chap',
            ['email' => self::EMAIL, 'challenge' => self::CHALLENGE, 'response' => self::RESPONSE]
        );
        $response = $this->client->getResponse();
        $responseBody = json_decode((string) $response->getContent(), true);

        $this->assertEquals(Response::HTTP_UNAUTHORIZED, $response->getStatusCode());
        $this->assertEquals($expectedBody, $responseBody);

        $this->assertLogFailure(
            'auth.chap.response',
            Response::HTTP_UNAUTHORIZED,
            $expectedBody,
            [[$this, 'assertLogRequestValidValues']]
        );
    }

    /**
     * @return void
     * @throws Exception
     */
    public function testUserNotActive(): void
    {
        $fixtures = new LocalFixtures();
        $fixtures->addUser(['enabled' => false]);
        $this->loadFixtures($fixtures);

        $expectedBody = [
            'status' => [
                'success' => false,
                'errors' => [['code' => 's2.e103.c11', 'message' => 'user not active']],
            ],
        ];

        $this->client->request(
            'POST',
            '/auth/chap',
            ['email' => self::EMAIL, 'challenge' => self::CHALLENGE, 'response' => self::RESPONSE]
        );
        $response = $this->client->getResponse();
        $responseBody = json_decode((string) $response->getContent(), true);

        $this->assertEquals(Response::HTTP_UNAUTHORIZED, $response->getStatusCode());
        $this->assertEquals($expectedBody, $responseBody);

        $this->assertLogFailure(
            'auth.chap.response',
            Response::HTTP_UNAUTHORIZED,
            $expectedBody,
            [[$this, 'assertLogRequestValidValues']]
        );
    }

    /**
     * @return void
     * @throws Exception
     */
    public function testMissingUserPassword(): void
    {
        $fixtures = new LocalFixtures();
        $fixtures->addUser(['password' => '']);
        $this->loadFixtures($fixtures);

        $expectedBody = [
            'status' => [
                'success' => false,
                'errors' => [['code' => 's2.e103.c12', 'message' => 'user authentication missing']],
            ],
        ];

        $this->client->request(
            'POST',
            '/auth/chap',
            ['email' => self::EMAIL, 'challenge' => self::CHALLENGE, 'response' => self::RESPONSE]
        );
        $response = $this->client->getResponse();
        $responseBody = json_decode((string) $response->getContent(), true);

        $this->assertEquals(Response::HTTP_UNAUTHORIZED, $response->getStatusCode());
        $this->assertEquals($expectedBody, $responseBody);

        $this->assertLogFailure(
            'auth.chap.response',
            Response::HTTP_UNAUTHORIZED,
            $expectedBody,
            [[$this, 'assertLogRequestValidValues']]
        );
    }

    /**
     * @return void
     * @throws Exception
     */
    public function testMissingUserPasswordSalt(): void
    {
        $fixtures = new LocalFixtures();
        $fixtures->addUser(['salt' => '']);
        $this->loadFixtures($fixtures);

        $expectedBody = [
            'status' => [
                'success' => false,
                'errors' => [['code' => 's2.e103.c12', 'message' => 'user authentication missing']],
            ],
        ];

        $this->client->request(
            'POST',
            '/auth/chap',
            ['email' => self::EMAIL, 'challenge' => self::CHALLENGE, 'response' => self::RESPONSE]
        );
        $response = $this->client->getResponse();
        $responseBody = json_decode((string) $response->getContent(), true);

        $this->assertEquals(Response::HTTP_UNAUTHORIZED, $response->getStatusCode());
        $this->assertEquals($expectedBody, $responseBody);

        $this->assertLogFailure(
            'auth.chap.response',
            Response::HTTP_UNAUTHORIZED,
            $expectedBody,
            [[$this, 'assertLogRequestValidValues']]
        );
    }

    /**
     * @return void
     * @throws Exception
     */
    public function testUserInCoolDown(): void
    {
        $fixtures = new LocalFixtures();
        $fixtures->addUser(['coolDown' => new DateTime('+5 minutes')]);
        $this->loadFixtures($fixtures);

        $expectedBody = [
            'status' => [
                'success' => false,
                'errors' => [['code' => 's2.e103.c14', 'message' => 'user in cool down']],
            ],
        ];

        $this->client->request(
            'POST',
            '/auth/chap',
            ['email' => self::EMAIL, 'challenge' => self::CHALLENGE, 'response' => self::RESPONSE]
        );
        $response = $this->client->getResponse();
        $responseBody = json_decode((string) $response->getContent(), true);

        $this->assertEquals(Response::HTTP_UNAUTHORIZED, $response->getStatusCode());
        $this->assertEquals($expectedBody, $responseBody);

        $this->assertLogFailure(
            'auth.chap.response',
            Response::HTTP_UNAUTHORIZED,
            $expectedBody,
            [[$this, 'assertLogRequestValidValues']]
        );
    }

    /**
     * @return void
     * @throws Exception
     */
    public function testUserWithoutChallenge(): void
    {
        $fixtures = new LocalFixtures();
        $fixtures->addUser();
        $this->loadFixtures($fixtures);

        $expectedBody = [
            'status' => [
                'success' => false,
                'errors' => [['code' => 's2.e103.c15', 'message' => 'invalid challenge']],
            ],
        ];

        $this->client->request(
            'POST',
            '/auth/chap',
            ['email' => self::EMAIL, 'challenge' => self::CHALLENGE, 'response' => self::RESPONSE]
        );
        $response = $this->client->getResponse();
        $responseBody = json_decode((string) $response->getContent(), true);

        $this->assertEquals(Response::HTTP_UNAUTHORIZED, $response->getStatusCode());
        $this->assertEquals($expectedBody, $responseBody);

        $this->assertLogFailure(
            'auth.chap.response',
            Response::HTTP_UNAUTHORIZED,
            $expectedBody,
            [[$this, 'assertLogRequestValidValues']]
        );
    }

    /**
     * @return void
     * @throws Exception
     */
    public function testInvalidChallenge(): void
    {
        $fixtures = new LocalFixtures();
        $user = $fixtures->addUser();
        $fixtures->addChallenge(['user' => $user, 'challenge' => '__dummy_other_challenge__']);
        $this->loadFixtures($fixtures);

        $expectedBody = [
            'status' => [
                'success' => false,
                'errors' => [['code' => 's2.e103.c15', 'message' => 'invalid challenge']],
            ],
        ];

        $this->client->request(
            'POST',
            '/auth/chap',
            ['email' => self::EMAIL, 'challenge' => self::CHALLENGE, 'response' => self::RESPONSE]
        );
        $response = $this->client->getResponse();
        $responseBody = json_decode((string) $response->getContent(), true);

        $this->assertEquals(Response::HTTP_UNAUTHORIZED, $response->getStatusCode());
        $this->assertEquals($expectedBody, $responseBody);

        $this->assertLogFailure(
            'auth.chap.response',
            Response::HTTP_UNAUTHORIZED,
            $expectedBody,
            [[$this, 'assertLogRequestValidValues']]
        );
    }

    /**
     * @return void
     * @throws Exception
     */
    public function testInvalidChallengeDecreaseUserTries(): void
    {
        $fixtures = new LocalFixtures();
        $user = $fixtures->addUser();
        $fixtures->addChallenge(['user' => $user]);
        $this->loadFixtures($fixtures);

        $expectedBody = [
            'status' => [
                'success' => false,
                'errors' => [['code' => 's2.e103.c16', 'message' => 'invalid response']],
            ],
        ];

        $this->client->request(
            'POST',
            '/auth/chap',
            ['email' => self::EMAIL, 'challenge' => self::CHALLENGE, 'response' => '__invalid_response__']
        );
        $response = $this->client->getResponse();
        $responseBody = json_decode((string) $response->getContent(), true);

        $this->assertEquals(Response::HTTP_UNAUTHORIZED, $response->getStatusCode());
        $this->assertEquals($expectedBody, $responseBody);

        $challenges = $this->localManager->getRepository(Challenge::class)->findAll();
        $this->assertCount(1, $challenges);
        $this->assertEquals(new DateTime('1900-01-01'), $challenges[0]->getTtl());

        $users = $this->localManager->getRepository(User::class)->findAll();
        $this->assertCount(1, $users);
        $this->assertEquals(2, $users[0]->getTries());
        $this->assertEquals(new DateTime('1900-01-01'), $users[0]->getCoolDown());

        $this->assertLogFailure(
            'auth.chap.response',
            Response::HTTP_UNAUTHORIZED,
            $expectedBody,
            [[$this, 'assertLogRequestInvalidValues']]
        );
    }

    /**
     * @return void
     * @throws Exception
     */
    public function testSetUserInCoolDownOnFailAllTries(): void
    {
        $fixtures = new LocalFixtures();
        $user = $fixtures->addUser(['tries' => 1]);
        $fixtures->addChallenge(['user' => $user]);
        $this->loadFixtures($fixtures);

        $expectedBody = [
            'status' => [
                'success' => false,
                'errors' => [['code' => 's2.e103.c16', 'message' => 'invalid response']],
            ],
        ];

        $this->client->request(
            'POST',
            '/auth/chap',
            ['email' => self::EMAIL, 'challenge' => self::CHALLENGE, 'response' => '__invalid_response__']
        );
        $response = $this->client->getResponse();
        $responseBody = json_decode((string) $response->getContent(), true);

        $this->assertEquals(Response::HTTP_UNAUTHORIZED, $response->getStatusCode());
        $this->assertEquals($expectedBody, $responseBody);

        $challenges = $this->localManager->getRepository(Challenge::class)->findAll();
        $this->assertCount(1, $challenges);
        $this->assertEquals(new DateTime('1900-01-01'), $challenges[0]->getTtl());

        $users = $this->localManager->getRepository(User::class)->findAll();
        $this->assertCount(1, $users);
        $this->assertEquals(0, $users[0]->getTries());
        $this->assertNotEquals(new DateTime('1900-01-01'), $users[0]->getCoolDown());

        $this->assertLogFailure(
            'auth.chap.response',
            Response::HTTP_UNAUTHORIZED,
            $expectedBody,
            [[$this, 'assertLogRequestInvalidValues']]
        );
    }

    /**
     * @return void
     * @throws Exception
     */
    public function testReturnActiveTokenIfExists(): void
    {
        $fixtures = new LocalFixtures();
        $user = $fixtures->addUser();
        $fixtures->addChallenge(['user' => $user]);
        $fixtures->addToken(['user' => $user]);
        $this->loadFixtures($fixtures);

        $this->client->request(
            'POST',
            '/auth/chap',
            ['email' => self::EMAIL, 'challenge' => self::CHALLENGE, 'response' => self::RESPONSE]
        );
        $response = $this->client->getResponse();
        $responseBody = json_decode((string) $response->getContent(), true);

        $this->assertEquals(Response::HTTP_CREATED, $response->getStatusCode());

        $tokens = $this->localManager->getRepository(Token::class)->findAll();
        $this->assertCount(1, $tokens);
        $this->assertEquals($user->getId(), $tokens[0]->getUser()?->getId());

        $challenges = $this->localManager->getRepository(Challenge::class)->findAll();
        $this->assertCount(1, $challenges);
        $this->assertEquals(new DateTime('1900-01-01'), $challenges[0]->getTtl());
        $this->assertEquals($user->getId(), $challenges[0]->getUser()?->getId());
        $this->assertEquals($tokens[0]->getId(), $challenges[0]->getToken()?->getId());

        $users = $this->localManager->getRepository(User::class)->findAll();
        $this->assertCount(1, $users);
        $this->assertEquals(3, $users[0]->getTries());
        $this->assertEquals(new DateTime('1900-01-01'), $users[0]->getCoolDown());

        $this->assertEquals($tokens[0]->getJwt(), $responseBody['data']['jwt']);
        $this->assertEquals($tokens[0]->getRecover(), $responseBody['data']['recover']);

        $this->assertLogSuccess(
            'auth.chap.response',
            Response::HTTP_CREATED,
            [[$this, 'assertLogRequestValidValues']]
        );
    }

    /**
     * @return void
     * @throws Exception
     */
    public function testCreateToken(): void
    {
        $fixtures = new LocalFixtures();
        $user = $fixtures->addUser();
        $fixtures->addChallenge(['user' => $user]);
        $this->loadFixtures($fixtures);

        $this->client->request(
            'POST',
            '/auth/chap',
            ['email' => self::EMAIL, 'challenge' => self::CHALLENGE, 'response' => self::RESPONSE]
        );
        $response = $this->client->getResponse();
        $responseBody = json_decode((string) $response->getContent(), true);

        $this->assertEquals(Response::HTTP_CREATED, $response->getStatusCode());

        $tokens = $this->localManager->getRepository(Token::class)->findAll();
        $this->assertCount(1, $tokens);
        $this->assertEquals($user->getId(), $tokens[0]->getUser()?->getId());

        $challenges = $this->localManager->getRepository(Challenge::class)->findAll();
        $this->assertCount(1, $challenges);
        $this->assertEquals(new DateTime('1900-01-01'), $challenges[0]->getTtl());
        $this->assertEquals($user->getId(), $challenges[0]->getUser()?->getId());
        $this->assertEquals($tokens[0]->getId(), $challenges[0]->getToken()?->getId());

        $users = $this->localManager->getRepository(User::class)->findAll();
        $this->assertCount(1, $users);
        $this->assertEquals(3, $users[0]->getTries());
        $this->assertEquals(new DateTime('1900-01-01'), $users[0]->getCoolDown());

        $this->assertEquals($tokens[0]->getJwt(), $responseBody['data']['jwt']);
        $this->assertEquals($tokens[0]->getRecover(), $responseBody['data']['recover']);

        $this->assertLogSuccess(
            'auth.chap.response',
            Response::HTTP_CREATED,
            [[$this, 'assertLogRequestValidValues']]
        );
    }

    /**
     * @param array<int, mixed> $records
     * @return void
     */
    public function assertLogRequestValidValues(array $records)
    {
        foreach ($records as $record) {
            $this->assertLogRequestBody($record, [
                'email' => self::EMAIL,
                'challenge' => self::CHALLENGE,
                'response' => self::RESPONSE
            ]);
        }
    }

    /**
     * @param array<int, mixed> $records
     * @return void
     */
    public function assertLogRequestInvalidValues(array $records)
    {
        foreach ($records as $record) {
            $this->assertLogRequestBody($record, [
                'email' => self::EMAIL,
                'challenge' => self::CHALLENGE,
                'response' => '__invalid_response__'
            ]);
        }
    }
}
