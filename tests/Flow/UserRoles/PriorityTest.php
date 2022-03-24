<?php

namespace App\Tests\Flow\UserRoles;

use App\Model\Entity\Local\UserRole;
use App\Tests\Flow\EndpointTester;
use App\Tests\LocalFixtures;
use DateTime;
use Exception;
use Symfony\Component\HttpFoundation\Response;

class PriorityTest extends EndpointTester
{
    /** @var int */
    private const PRIORITY = 3;

    /** @var array<string, mixed> */
    private const PARAMS = [
        'priority' => self::PRIORITY,
    ];

    /** @var string */
    private string $userId;

    /** @var string */
    private string $roleId;

    /**
     * @return void
     */
    public function testUserNotFound(): void
    {
        $expectedBody = [
            'status' => [
                'success' => false,
                'errors' => [['code' => 's2.e605.c10', 'message' => 'user not found']],
            ],
        ];

        $this->userId = "1";
        $this->roleId = "1";
        $this->client->request(
            'POST',
            '/users/' . $this->userId . '/roles/' . $this->roleId . '/priority',
            self::PARAMS,
            [],
            self::HEADERS
        );
        $response = $this->client->getResponse();
        $responseBody = json_decode((string) $response->getContent(), true);

        $this->assertEquals(Response::HTTP_NOT_FOUND, $response->getStatusCode());
        $this->assertEquals($expectedBody, $responseBody);

        $this->assertLogFailure(
            'users.roles.priority',
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
                'errors' => [['code' => 's2.e605.c10', 'message' => 'user not found']],
            ],
        ];

        $this->userId = (string) $user->getId();
        $this->roleId = "1";
        $this->client->request(
            'POST',
            '/users/' . $this->userId . '/roles/' . $this->roleId . '/priority',
            self::PARAMS,
            [],
            self::HEADERS
        );
        $response = $this->client->getResponse();
        $responseBody = json_decode((string) $response->getContent(), true);

        $this->assertEquals(Response::HTTP_NOT_FOUND, $response->getStatusCode());
        $this->assertEquals($expectedBody, $responseBody);

        $this->assertLogFailure(
            'users.roles.priority',
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
                'errors' => [['code' => 's2.e605.c23', 'message' => 'role not found']],
            ],
        ];

        $this->userId = (string) $user->getId();
        $this->roleId = "1";
        $this->client->request(
            'POST',
            '/users/' . $this->userId . '/roles/' . $this->roleId . '/priority',
            self::PARAMS,
            [],
            self::HEADERS
        );
        $response = $this->client->getResponse();
        $responseBody = json_decode((string) $response->getContent(), true);

        $this->assertEquals(Response::HTTP_NOT_FOUND, $response->getStatusCode());
        $this->assertEquals($expectedBody, $responseBody);

        $this->assertLogFailure(
            'users.roles.priority',
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
        $role = $fixtures->addRole(['deletedAt' => new DateTime()]);
        $this->loadFixtures($fixtures);

        $expectedBody = [
            'status' => [
                'success' => false,
                'errors' => [['code' => 's2.e605.c23', 'message' => 'role not found']],
            ],
        ];

        $this->userId = (string) $user->getId();
        $this->roleId = (string) $role->getId();
        $this->client->request(
            'POST',
            '/users/' . $this->userId . '/roles/' . $this->roleId . '/priority',
            self::PARAMS,
            [],
            self::HEADERS
        );
        $response = $this->client->getResponse();
        $responseBody = json_decode((string) $response->getContent(), true);

        $this->assertEquals(Response::HTTP_NOT_FOUND, $response->getStatusCode());
        $this->assertEquals($expectedBody, $responseBody);

        $this->assertLogFailure(
            'users.roles.priority',
            Response::HTTP_NOT_FOUND,
            $expectedBody,
            [[$this, 'assertLogRequestValues']]
        );
    }

    /**
     * @return void
     * @throws Exception
     */
    public function testRelationNotFound(): void
    {
        $fixtures = new LocalFixtures();
        $user = $fixtures->addUser();
        $role = $fixtures->addRole();
        $this->loadFixtures($fixtures);

        $expectedBody = [
            'status' => [
                'success' => false,
                'errors' => [[
                    'code' => 's2.e605.c29',
                    'message' => 'user role not found',
                ]],
            ],
        ];

        $this->userId = (string) $user->getId();
        $this->roleId = (string) $role->getId();
        $this->client->request(
            'POST',
            '/users/' . $this->userId . '/roles/' . $this->roleId . '/priority',
            self::PARAMS,
            [],
            self::HEADERS
        );
        $response = $this->client->getResponse();
        $responseBody = json_decode((string) $response->getContent(), true);

        $this->assertEquals(Response::HTTP_CONFLICT, $response->getStatusCode());
        $this->assertEquals($expectedBody, $responseBody);

        $this->assertLogFailure(
            'users.roles.priority',
            Response::HTTP_CONFLICT,
            $expectedBody,
            [[$this, 'assertLogRequestValues']]
        );
    }

    /**
     * @return void
     * @throws Exception
     */
    public function testRelationDeleted(): void
    {
        $fixtures = new LocalFixtures();
        $user = $fixtures->addUser();
        $role = $fixtures->addRole();
        $fixtures->addUserRole(['user' => $user, 'role' => $role, 'deletedAt' => new DateTime()]);
        $this->loadFixtures($fixtures);

        $expectedBody = [
            'status' => [
                'success' => false,
                'errors' => [[
                    'code' => 's2.e605.c29',
                    'message' => 'user role not found',
                ]],
            ],
        ];

        $this->userId = (string) $user->getId();
        $this->roleId = (string) $role->getId();
        $this->client->request(
            'POST',
            '/users/' . $this->userId . '/roles/' . $this->roleId . '/priority',
            self::PARAMS,
            [],
            self::HEADERS
        );
        $response = $this->client->getResponse();
        $responseBody = json_decode((string) $response->getContent(), true);

        $this->assertEquals(Response::HTTP_CONFLICT, $response->getStatusCode());
        $this->assertEquals($expectedBody, $responseBody);

        $this->assertLogFailure(
            'users.roles.priority',
            Response::HTTP_CONFLICT,
            $expectedBody,
            [[$this, 'assertLogRequestValues']]
        );
    }

    /**
     * @return void
     * @throws Exception
     */
    public function testCorrectlyUpdatePriority(): void
    {
        $fixtures = new LocalFixtures();
        $fixtures->addUser(['email' => 'root@email.com']);
        $user = $fixtures->addUser(['email' => 'email@email.com']);
        $role1 = $fixtures->addRole(['name' => '__dummy_name_1__']);
        $role2 = $fixtures->addRole(['name' => '__dummy_name_2__']);
        $role3 = $fixtures->addRole(['name' => '__dummy_name_3__']);
        $fixtures->addUserRole(['user' => $user, 'role' => $role1, 'priority' => 1]);
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
        $this->roleId = (string) $role1->getId();
        $this->client->request(
            'POST',
            '/users/' . $this->userId . '/roles/' . $this->roleId . '/priority',
            self::PARAMS,
            [],
            self::HEADERS
        );
        $response = $this->client->getResponse();
        $responseBody = json_decode((string) $response->getContent(), true);

        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
        $expectedBody['data']['role']['updatedAt'] = $responseBody['data']['role']['updatedAt'];
        $this->assertEquals($expectedBody, $responseBody);

        $rels = $this->localManager->getRepository(UserRole::class)->findAll();
        $this->assertCount(3, $rels);
        $this->assertNull($rels[0]->getDeletedAt());
        $this->assertEquals(3, $rels[0]->getPriority());
        $this->assertEquals(2, $rels[1]->getPriority());
        $this->assertEquals(4, $rels[2]->getPriority());

        $this->assertLogSuccess(
            'users.roles.priority',
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
            $this->assertLogRequestAttr($record, 'userId', $this->userId);
            $this->assertLogRequestAttr($record, 'roleId', $this->roleId);
            $this->assertLogRequestBody($record, self::PARAMS);
        }
    }
}
