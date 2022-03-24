<?php

namespace App\Tests\Flow\UserRoles;

use App\Model\Entity\Local\UserRole;
use App\Tests\Flow\EndpointTester;
use App\Tests\LocalFixtures;
use DateTime;
use Exception;
use Symfony\Component\HttpFoundation\Response;

class AddTest extends EndpointTester
{
    /** @var int */
    private const ROLE_ID = 1;

    /** @var int */
    private const PRIORITY = 2;

    /** @var array<string, mixed> */
    private const PARAMS = [
        'roleId' => self::ROLE_ID,
        'enabled' => true,
        'priority' => self::PRIORITY,
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
                'errors' => [['code' => 's2.e601.c10', 'message' => 'user not found']],
            ],
        ];

        $this->userId = "1";
        $this->client->request('POST', '/users/1/roles', self::PARAMS, [], self::HEADERS);
        $response = $this->client->getResponse();
        $responseBody = json_decode((string) $response->getContent(), true);

        $this->assertEquals(Response::HTTP_NOT_FOUND, $response->getStatusCode());
        $this->assertEquals($expectedBody, $responseBody);

        $this->assertLogFailure(
            'users.roles.add',
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
        $user = $fixtures->addUser(['deletedAt' => new DateTime()]);
        $this->loadFixtures($fixtures);

        $expectedBody = [
            'status' => [
                'success' => false,
                'errors' => [['code' => 's2.e601.c10', 'message' => 'user not found']],
            ],
        ];

        $this->userId = (string) $user->getId();
        $this->client->request(
            'POST',
            '/users/' . $this->userId . '/roles',
            self::PARAMS,
            [],
            self::HEADERS
        );
        $response = $this->client->getResponse();
        $responseBody = json_decode((string) $response->getContent(), true);

        $this->assertEquals(Response::HTTP_NOT_FOUND, $response->getStatusCode());
        $this->assertEquals($expectedBody, $responseBody);

        $this->assertLogFailure(
            'users.roles.add',
            Response::HTTP_NOT_FOUND,
            $expectedBody,
            [[$this, 'assertLogRequestValues']]
        );
    }

    /**
     * @return void
     * @throws Exception
     */
    public function testRoleNotFound(): void
    {
        $fixtures = new LocalFixtures();
        $user = $fixtures->addUser();
        $this->loadFixtures($fixtures);

        $expectedBody = [
            'status' => [
                'success' => false,
                'errors' => [['code' => 's2.e601.c23', 'message' => 'role not found']],
            ],
        ];

        $this->userId = (string) $user->getId();
        $this->client->request(
            'POST',
            '/users/' . $this->userId . '/roles',
            self::PARAMS,
            [],
            self::HEADERS
        );
        $response = $this->client->getResponse();
        $responseBody = json_decode((string) $response->getContent(), true);

        $this->assertEquals(Response::HTTP_NOT_FOUND, $response->getStatusCode());
        $this->assertEquals($expectedBody, $responseBody);

        $this->assertLogFailure(
            'users.roles.add',
            Response::HTTP_NOT_FOUND,
            $expectedBody,
            [[$this, 'assertLogRequestValues']]
        );
    }

    /**
     * @return void
     * @throws Exception
     */
    public function testRoleDeleted(): void
    {
        $fixtures = new LocalFixtures();
        $user = $fixtures->addUser();
        $fixtures->addRole(['deletedAt' => new DateTime()]);
        $this->loadFixtures($fixtures);

        $expectedBody = [
            'status' => [
                'success' => false,
                'errors' => [['code' => 's2.e601.c23', 'message' => 'role not found']],
            ],
        ];

        $this->userId = (string) $user->getId();
        $this->client->request(
            'POST',
            '/users/' . $this->userId . '/roles',
            self::PARAMS,
            [],
            self::HEADERS
        );
        $response = $this->client->getResponse();
        $responseBody = json_decode((string) $response->getContent(), true);

        $this->assertEquals(Response::HTTP_NOT_FOUND, $response->getStatusCode());
        $this->assertEquals($expectedBody, $responseBody);

        $this->assertLogFailure(
            'users.roles.add',
            Response::HTTP_NOT_FOUND,
            $expectedBody,
            [[$this, 'assertLogRequestValues']]
        );
    }

    /**
     * @return void
     * @throws Exception
     */
    public function testDuplicateAssociation(): void
    {
        $fixtures = new LocalFixtures();
        $user = $fixtures->addUser();
        $role = $fixtures->addRole();
        $fixtures->addUserRole(['user' => $user, 'role' => $role]);
        $this->loadFixtures($fixtures);

        $expectedBody = [
            'status' => [
                'success' => false,
                'errors' => [[
                    'code' => 's2.e601.c30',
                    'message' => 'user role relation already existent in the database',
                ]],
            ],
        ];

        $this->userId = (string) $user->getId();
        $this->client->request(
            'POST',
            '/users/' . $this->userId . '/roles',
            self::PARAMS,
            [],
            self::HEADERS
        );
        $response = $this->client->getResponse();
        $responseBody = json_decode((string) $response->getContent(), true);

        $this->assertEquals(Response::HTTP_CONFLICT, $response->getStatusCode());
        $this->assertEquals($expectedBody, $responseBody);

        $rels = $this->localManager->getRepository(UserRole::class)->findAll();
        $this->assertCount(1, $rels);
        $this->assertNull($rels[0]->getDeletedAt());

        $this->assertLogFailure(
            'users.roles.add',
            Response::HTTP_CONFLICT,
            $expectedBody,
            [[$this, 'assertLogRequestValues']]
        );
    }

    /**
     * @return void
     * @throws Exception
     */
    public function testReinstateDeletedAssociation(): void
    {
        $fixtures = new LocalFixtures();
        $fixtures->addUser(['email' => 'root@email.com']);
        $user = $fixtures->addUser(['email' => 'email@email.com']);
        $role1 = $fixtures->addRole(['name' => '__dummy_name_1__']);
        $role2 = $fixtures->addRole(['name' => '__dummy_name_2__']);
        $role3 = $fixtures->addRole(['name' => '__dummy_name_3__']);
        $fixtures->addUserRole(['user' => $user, 'role' => $role1, 'deletedAt' => new DateTime(), 'priority' => 1]);
        $fixtures->addUserRole(['user' => $user, 'role' => $role2, 'priority' => 2]);
        $fixtures->addUserRole(['user' => $user, 'role' => $role3, 'priority' => 3]);
        $this->loadFixtures($fixtures);

        $expectedBody = [
            'status' => [
                'success' => true,
                'errors' => [],
            ],
            'data' => [
                'role' => [
                    'id' => 1,
                    'enabled' => true,
                    'priority' => self::PRIORITY,
                    'name' => '__dummy_name_1__',
                    'description' => '__dummy_description__',
                    'createdAt' => '2000-01-01 00:00:00',
                    'updatedAt' => '2000-01-01 00:00:00',
                    'updatedBy' => 'root@email.com',
                ],
            ]
        ];

        $this->userId = (string) $user->getId();
        $this->client->request(
            'POST',
            '/users/' . $this->userId . '/roles',
            self::PARAMS,
            [],
            self::HEADERS
        );
        $response = $this->client->getResponse();
        $responseBody = json_decode((string) $response->getContent(), true);

        $this->assertEquals(Response::HTTP_CREATED, $response->getStatusCode());
        $expectedBody['data']['role']['updatedAt'] = $responseBody['data']['role']['updatedAt'];
        $this->assertEquals($expectedBody, $responseBody);

        $rels = $this->localManager->getRepository(UserRole::class)->findAll();
        $this->assertCount(3, $rels);
        $this->assertNull($rels[0]->getDeletedAt());
        $this->assertEquals(2, $rels[0]->getPriority());
        $this->assertEquals(3, $rels[1]->getPriority());
        $this->assertEquals(4, $rels[2]->getPriority());

        $this->assertLogSuccess(
            'users.roles.add',
            Response::HTTP_CREATED,
            [[$this, 'assertLogRequestValues']]
        );
    }

    /**
     * @return void
     * @throws Exception
     */
    public function testAddTheRelation(): void
    {
        $fixtures = new LocalFixtures();
        $fixtures->addUser(['email' => 'root@email.com']);
        $user = $fixtures->addUser(['email' => 'email@email.com']);
        $fixtures->addRole(['name' => '__dummy_name_1__']);
        $role2 = $fixtures->addRole(['name' => '__dummy_name_2__']);
        $role3 = $fixtures->addRole(['name' => '__dummy_name_3__']);
        $fixtures->addUserRole(['user' => $user, 'role' => $role2, 'priority' => 2]);
        $fixtures->addUserRole(['user' => $user, 'role' => $role3, 'priority' => 3]);
        $this->loadFixtures($fixtures);

        $expectedBody = [
            'status' => [
                'success' => true,
                'errors' => [],
            ],
            'data' => [
                'role' => [
                    'id' => 3,
                    'enabled' => true,
                    'priority' => self::PRIORITY,
                    'name' => '__dummy_name_1__',
                    'description' => '__dummy_description__',
                    'createdAt' => '2000-01-01 00:00:00',
                    'createdBy' => 'root@email.com',
                    'updatedAt' => '2000-01-01 00:00:00',
                    'updatedBy' => 'root@email.com',
                ],
            ]
        ];

        $this->userId = (string) $user->getId();
        $this->client->request(
            'POST',
            '/users/' . $this->userId . '/roles',
            self::PARAMS,
            [],
            self::HEADERS
        );
        $response = $this->client->getResponse();
        $responseBody = json_decode((string) $response->getContent(), true);

        $this->assertEquals(Response::HTTP_CREATED, $response->getStatusCode());
        $expectedBody['data']['role']['createdAt'] = $responseBody['data']['role']['createdAt'];
        $expectedBody['data']['role']['updatedAt'] = $responseBody['data']['role']['updatedAt'];
        $this->assertEquals($expectedBody, $responseBody);

        $rels = $this->localManager->getRepository(UserRole::class)->findAll();
        $this->assertCount(3, $rels);
        $this->assertNull($rels[0]->getDeletedAt());
        $this->assertEquals(3, $rels[0]->getPriority());
        $this->assertEquals(4, $rels[1]->getPriority());
        $this->assertEquals(2, $rels[2]->getPriority());

        $this->assertLogSuccess(
            'users.roles.add',
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
            $this->assertLogRequestAttr($record, 'userId', $this->userId);
            $this->assertLogRequestBody($record, self::PARAMS);
        }
    }
}
