<?php

namespace App\Tests\Flow\Auth;

use App\Model\Entity\Local\Challenge;
use App\Tests\Flow\EndpointTester;
use App\Tests\LocalFixtures;
use DateTime;
use Exception;
use Symfony\Component\HttpFoundation\Response;

class AuthChapRequestTest extends EndpointTester
{
    /** @var string */
    private const EMAIL = 'email@email.com';

    /**
     * @return void
     */
    public function testDisabled(): void
    {
        $expectedBody = [
            'status' => [
                'success' => false,
                'errors' => [['code' => 's2.e102.c2', 'message' => 'Not enabled']],
            ],
        ];

        putenv('HIPPY_AUTH_CHAP_ENABLED=false');
        $this->client->request('GET', '/auth/chap', ['email' => self::EMAIL]);
        putenv('HIPPY_AUTH_CHAP_ENABLED=');
        $response = $this->client->getResponse();
        $responseBody = json_decode((string) $response->getContent(), true);

        $this->assertEquals(Response::HTTP_SERVICE_UNAVAILABLE, $response->getStatusCode());
        $this->assertEquals($expectedBody, $responseBody);

        $this->assertLogFailure(
            'auth.chap.request',
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
                'errors' => [['code' => 's2.e102.c11', 'message' => 'user not active']],
            ],
        ];

        $this->client->request('GET', '/auth/chap', ['email' => self::EMAIL]);
        $response = $this->client->getResponse();
        $responseBody = json_decode((string) $response->getContent(), true);

        $this->assertEquals(Response::HTTP_UNAUTHORIZED, $response->getStatusCode());
        $this->assertEquals($expectedBody, $responseBody);

        $this->assertLogFailure(
            'auth.chap.request',
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
        $fixtures->addUser(['deletedAt' => new DateTime()]);
        $this->loadFixtures($fixtures);

        $expectedBody = [
            'status' => [
                'success' => false,
                'errors' => [['code' => 's2.e102.c11', 'message' => 'user not active']],
            ],
        ];

        $this->client->request('GET', '/auth/chap', ['email' => self::EMAIL]);
        $response = $this->client->getResponse();
        $responseBody = json_decode((string) $response->getContent(), true);

        $this->assertEquals(Response::HTTP_UNAUTHORIZED, $response->getStatusCode());
        $this->assertEquals($expectedBody, $responseBody);

        $this->assertLogFailure(
            'auth.chap.request',
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
        $fixtures->addUser(['enabled' => false]);
        $this->loadFixtures($fixtures);

        $expectedBody = [
            'status' => [
                'success' => false,
                'errors' => [['code' => 's2.e102.c11', 'message' => 'user not active']],
            ],
        ];

        $this->client->request('GET', '/auth/chap', ['email' => self::EMAIL]);
        $response = $this->client->getResponse();
        $responseBody = json_decode((string) $response->getContent(), true);

        $this->assertEquals(Response::HTTP_UNAUTHORIZED, $response->getStatusCode());
        $this->assertEquals($expectedBody, $responseBody);

        $this->assertLogFailure(
            'auth.chap.request',
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
        $fixtures->addUser(['password' => '']);
        $this->loadFixtures($fixtures);

        $expectedBody = [
            'status' => [
                'success' => false,
                'errors' => [['code' => 's2.e102.c12', 'message' => 'user authentication missing']],
            ],
        ];

        $this->client->request('GET', '/auth/chap', ['email' => self::EMAIL]);
        $response = $this->client->getResponse();
        $responseBody = json_decode((string) $response->getContent(), true);

        $this->assertEquals(Response::HTTP_UNAUTHORIZED, $response->getStatusCode());
        $this->assertEquals($expectedBody, $responseBody);

        $this->assertLogFailure(
            'auth.chap.request',
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
        $fixtures->addUser(['salt' => '']);
        $this->loadFixtures($fixtures);

        $expectedBody = [
            'status' => [
                'success' => false,
                'errors' => [['code' => 's2.e102.c12', 'message' => 'user authentication missing']],
            ],
        ];

        $this->client->request('GET', '/auth/chap', ['email' => self::EMAIL]);
        $response = $this->client->getResponse();
        $responseBody = json_decode((string) $response->getContent(), true);

        $this->assertEquals(Response::HTTP_UNAUTHORIZED, $response->getStatusCode());
        $this->assertEquals($expectedBody, $responseBody);

        $this->assertLogFailure(
            'auth.chap.request',
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
        $fixtures->addUser(['coolDown' => new DateTime('+5 minutes')]);
        $this->loadFixtures($fixtures);

        $expectedBody = [
            'status' => [
                'success' => false,
                'errors' => [['code' => 's2.e102.c14', 'message' => 'user in cool down']],
            ],
        ];

        $this->client->request('GET', '/auth/chap', ['email' => self::EMAIL]);
        $response = $this->client->getResponse();
        $responseBody = json_decode((string) $response->getContent(), true);

        $this->assertEquals(Response::HTTP_UNAUTHORIZED, $response->getStatusCode());
        $this->assertEquals($expectedBody, $responseBody);

        $this->assertLogFailure(
            'auth.chap.request',
            Response::HTTP_UNAUTHORIZED,
            $expectedBody,
            [[$this, 'assertLogRequestValues']]
        );
    }

    /**
     * @return void
     * @throws Exception
     */
    public function testReturnExistingChallenge(): void
    {
        $fixtures = new LocalFixtures();
        $user = $fixtures->addUser();
        $challenge = $fixtures->addChallenge(['user' => $user]);
        $this->loadFixtures($fixtures);

        $expectedBody = [
            'status' => ['success' => true, 'errors' => []],
            'data' => [
                'challenge' => $challenge->getChallenge(),
                'challengeSalt' => $challenge->getSalt(),
                'passwordSalt' => $user->getSalt(),
                'ttl' => $challenge->getTtl()?->format('Y-m-d H:i:s'),
            ],
        ];

        $this->client->request('GET', '/auth/chap', ['email' => self::EMAIL]);
        $response = $this->client->getResponse();
        $responseBody = json_decode((string) $response->getContent(), true);

        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
        $this->assertEquals($expectedBody, $responseBody);

        $challenges = $this->localManager->getRepository(Challenge::class)->findAll();
        $this->assertCount(1, $challenges);
        $this->assertEquals($responseBody['data']['challenge'], $challenges[0]->getChallenge());
        $this->assertEquals($responseBody['data']['challengeSalt'], $challenges[0]->getSalt());
        $this->assertEquals($responseBody['data']['passwordSalt'], $user->getSalt());
        $this->assertEquals(new DateTime($responseBody['data']['ttl']), $challenges[0]->getTtl());

        $this->assertLogSuccess(
            'auth.chap.request',
            Response::HTTP_OK,
            [[$this, 'assertLogRequestValues']]
        );
    }

    /**
     * @return void
     * @throws Exception
     */
    public function testReturnCreatedChallenge(): void
    {
        $fixtures = new LocalFixtures();
        $user = $fixtures->addUser();
        $fixtures->addChallenge(['user' => $user, 'ttl' => new DateTime("-1 minute")]);
        $this->loadFixtures($fixtures);

        $this->client->request('GET', '/auth/chap', ['email' => self::EMAIL]);
        $response = $this->client->getResponse();
        $responseBody = json_decode((string) $response->getContent(), true);

        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());

        $challenges = $this->localManager->getRepository(Challenge::class)->findAll();
        $this->assertCount(2, $challenges);
        $this->assertEquals($responseBody['data']['challenge'], $challenges[1]->getChallenge());
        $this->assertEquals($responseBody['data']['challengeSalt'], $challenges[1]->getSalt());
        $this->assertEquals($responseBody['data']['passwordSalt'], $user->getSalt());
        $this->assertEquals($responseBody['data']['ttl'], $challenges[1]->getTtl()?->format('Y-m-d H:i:s'));

        $this->assertLogSuccess(
            'auth.chap.request',
            Response::HTTP_OK,
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
            $this->assertLogRequestQuery($record, 'email', self::EMAIL);
        }
    }
}
