<?php

namespace App\Tests\Flow\Users;

use App\Model\Entity\Local\User;
use App\Tests\Flow\EndpointTester;
use App\Tests\LocalFixtures;
use DateTime;
use Exception;
use Symfony\Component\HttpFoundation\Response;

class UpdateTest extends EndpointTester
{
    /** @var string */
    private const EMAIL = 'email@email.com';

    /** @var array<string, mixed> */
    private const PARAMS = [
        'enabled' => true,
        'email' => self::EMAIL,
    ];

    /** @var string */
    private string $userId;

    /**
     * @return void
     */
    public function testUserNotFound(): void
    {
        $expectedBody = [
            'status' => [
                'success' => false,
                'errors' => [['code' => 's2.e203.c10', 'message' => 'user not found']],
            ],
        ];

        $this->userId = "1";
        $this->client->request('POST', '/users/1', self::PARAMS, [], self::HEADERS);
        $response = $this->client->getResponse();
        $responseBody = json_decode((string) $response->getContent(), true);

        $this->assertEquals(Response::HTTP_NOT_FOUND, $response->getStatusCode());
        $this->assertEquals($expectedBody, $responseBody);

        $this->assertLogFailure(
            'users.update',
            Response::HTTP_NOT_FOUND,
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
                'errors' => [['code' => 's2.e203.c10', 'message' => 'user not found']],
            ],
        ];

        $this->userId = "1";
        $this->client->request('POST', '/users/1', self::PARAMS, [], self::HEADERS);
        $response = $this->client->getResponse();
        $responseBody = json_decode((string) $response->getContent(), true);

        $this->assertEquals(Response::HTTP_NOT_FOUND, $response->getStatusCode());
        $this->assertEquals($expectedBody, $responseBody);

        $this->assertLogFailure(
            'users.update',
            Response::HTTP_NOT_FOUND,
            $expectedBody,
            [[$this, 'assertLogRequestValues']]
        );
    }

    /**
     * @return void
     * @throws Exception
     */
    public function testDuplicateEmail(): void
    {
        $fixtures = new LocalFixtures();
        $fixtures->addUser();
        $user = $fixtures->addUser(['email' => '2.email@email.com']);
        $this->loadFixtures($fixtures);

        $expectedBody = [
            'status' => [
                'success' => false,
                'errors' => [['code' => 's2.e203.c22', 'message' => 'user email already existent in the database']],
            ],
        ];

        $this->userId = (string) $user->getId();
        $this->client->request('POST', '/users/' . $this->userId, self::PARAMS, [], self::HEADERS);
        $response = $this->client->getResponse();
        $responseBody = json_decode((string) $response->getContent(), true);

        $this->assertEquals(Response::HTTP_CONFLICT, $response->getStatusCode());
        $this->assertEquals($expectedBody, $responseBody);

        $this->assertLogFailure(
            'users.update',
            Response::HTTP_CONFLICT,
            $expectedBody,
            [[$this, 'assertLogRequestValues']]
        );
    }

    /**
     * @return void
     * @throws Exception
     */
    public function testUpdateUser(): void
    {
        $fixtures = new LocalFixtures();
        $fixtures->addUser(['email' => 'root@email.com']);
        $user = $fixtures->addUser(['email' => '2.email@email.com']);
        $this->loadFixtures($fixtures);

        $expectedBody = [
            'status' => [
                'success' => true,
                'errors' => [],
            ],
            'data' => [
                'user' => [
                    'id' => $user->getId(),
                    'enabled' => true,
                    'email' => self::EMAIL,
                    'tries' => 3,
                    'coolDown' => '1900-01-01 00:00:00',
                    'updatedBy' => 'root@email.com',
                ],
            ],
        ];

        $this->userId = (string) $user->getId();
        $this->client->request('POST', '/users/' . $this->userId, self::PARAMS, [], self::HEADERS);
        $response = $this->client->getResponse();
        $responseBody = json_decode((string) $response->getContent(), true);

        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
        $expectedBody['data']['user']['createdAt'] = $responseBody['data']['user']['createdAt'];
        $expectedBody['data']['user']['updatedAt'] = $responseBody['data']['user']['updatedAt'];
        $this->assertEquals($expectedBody, $responseBody);

        $users = $this->localManager->getRepository(User::class)->findAll();
        $this->assertCount(2, $users);
        $this->assertTrue($users[1]->isEnabled());
        $this->assertEquals($user->getId(), $users[1]->getId());
        $this->assertEquals(self::EMAIL, $users[1]->getEmail());

        $this->assertLogSuccess(
            'users.update',
            Response::HTTP_OK,
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
            $this->assertLogRequestAttr($record, "userId", $this->userId);
            $this->assertLogRequestBody($record, [
                'enabled' => true,
                'email' => self::EMAIL,
            ]);
        }
    }
}
