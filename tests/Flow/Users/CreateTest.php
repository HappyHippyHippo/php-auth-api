<?php

namespace App\Tests\Flow\Users;

use App\Model\Entity\Local\User;
use App\Tests\Flow\EndpointTester;
use App\Tests\LocalFixtures;
use Exception;
use Symfony\Component\HttpFoundation\Response;

class CreateTest extends EndpointTester
{
    /** @var string */
    private const EMAIL = 'email@email.com';

    /** @var string */
    private const PASSWORD = '__dummy_password__';

    /** @var array<string, mixed> */
    private const PARAMS = [
        'enabled' => true,
        'email' => self::EMAIL,
        'password' => self::PASSWORD,
    ];

    /**
     * @return void
     * @throws Exception
     */
    public function testDuplicateEmail(): void
    {
        $fixtures = new LocalFixtures();
        $fixtures->addUser();
        $this->loadFixtures($fixtures);

        $expectedBody = [
            'status' => [
                'success' => false,
                'errors' => [['code' => 's2.e202.c22', 'message' => 'user email already existent in the database']],
            ],
        ];

        $this->client->request('POST', '/users', self::PARAMS, [], self::HEADERS);
        $response = $this->client->getResponse();
        $responseBody = json_decode((string) $response->getContent(), true);

        $this->assertEquals(Response::HTTP_CONFLICT, $response->getStatusCode());
        $this->assertEquals($expectedBody, $responseBody);

        $users = $this->localManager->getRepository(User::class)->findAll();
        $this->assertCount(1, $users);

        $this->assertLogFailure(
            'users.create',
            Response::HTTP_CONFLICT,
            $expectedBody,
            [[$this, 'assertLogRequestValues']]
        );
    }

    /**
     * @return void
     * @throws Exception
     */
    public function testCreateUser(): void
    {
        $fixtures = new LocalFixtures();
        $fixtures->addUser(['email' => 'root@email.com']);
        $this->loadFixtures($fixtures);

        $expectedBody = [
            'status' => [
                'success' => true,
                'errors' => [],
            ],
            'data' => [
                'user' => [
                    'id' => 2,
                    'enabled' => true,
                    'email' => self::EMAIL,
                    'tries' => 3,
                    'coolDown' => '1900-01-01 00:00:00',
                    'createdAt' => '1900-01-01 00:00:00',
                    'createdBy' => 'root@email.com',
                    'updatedAt' => '1900-01-01 00:00:00',
                    'updatedBy' => 'root@email.com',
                ],
            ],
        ];

        $this->client->request('POST', '/users', self::PARAMS, [], self::HEADERS);
        $response = $this->client->getResponse();
        $responseBody = json_decode((string) $response->getContent(), true);

        $this->assertEquals(Response::HTTP_CREATED, $response->getStatusCode());
        $expectedBody['data']['user']['createdAt'] = $responseBody['data']['user']['createdAt'];
        $expectedBody['data']['user']['updatedAt'] = $responseBody['data']['user']['updatedAt'];
        $this->assertEquals($expectedBody, $responseBody);

        $users = $this->localManager->getRepository(User::class)->findAll();
        $this->assertCount(2, $users);
        $this->assertTrue($users[1]->isEnabled());
        $this->assertEquals(2, $users[1]->getId());
        $this->assertEquals(self::EMAIL, $users[1]->getEmail());

        $this->assertLogSuccess(
            'users.create',
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
            $this->assertLogRequestBody($record, [
                'enabled' => true,
                'email' => self::EMAIL,
                'password' => '******',
            ]);
        }
    }
}
