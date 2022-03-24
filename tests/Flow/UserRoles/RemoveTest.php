<?php

namespace App\Tests\Flow\UserRoles;

use App\Model\Entity\Local\UserRole;
use App\Tests\Flow\EndpointTester;
use App\Tests\LocalFixtures;
use DateTime;
use Exception;
use Symfony\Component\HttpFoundation\Response;

class RemoveTest extends EndpointTester
{
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
                'errors' => [['code' => 's2.e602.c10', 'message' => 'user not found']],
            ],
        ];

        $this->userId = "1";
        $this->roleId = "1";
        $this->client->request(
            'DELETE',
            '/users/' . $this->userId . '/roles/' . $this->roleId,
            [],
            [],
            self::HEADERS
        );
        $response = $this->client->getResponse();
        $responseBody = json_decode((string) $response->getContent(), true);

        $this->assertEquals(Response::HTTP_NOT_FOUND, $response->getStatusCode());
        $this->assertEquals($expectedBody, $responseBody);

        $this->assertLogFailure(
            'users.roles.remove',
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
                'errors' => [['code' => 's2.e602.c10', 'message' => 'user not found']],
            ],
        ];

        $this->userId = (string) $user->getId();
        $this->roleId = "1";
        $this->client->request(
            'DELETE',
            '/users/' . $this->userId . '/roles/' . $this->roleId,
            [],
            [],
            self::HEADERS
        );
        $response = $this->client->getResponse();
        $responseBody = json_decode((string) $response->getContent(), true);

        $this->assertEquals(Response::HTTP_NOT_FOUND, $response->getStatusCode());
        $this->assertEquals($expectedBody, $responseBody);

        $this->assertLogFailure(
            'users.roles.remove',
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
                'errors' => [['code' => 's2.e602.c23', 'message' => 'role not found']],
            ],
        ];

        $this->userId = (string) $user->getId();
        $this->roleId = "1";
        $this->client->request(
            'DELETE',
            '/users/' . $this->userId . '/roles/' . $this->roleId,
            [],
            [],
            self::HEADERS
        );
        $response = $this->client->getResponse();
        $responseBody = json_decode((string) $response->getContent(), true);

        $this->assertEquals(Response::HTTP_NOT_FOUND, $response->getStatusCode());
        $this->assertEquals($expectedBody, $responseBody);

        $this->assertLogFailure(
            'users.roles.remove',
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
                'errors' => [['code' => 's2.e602.c23', 'message' => 'role not found']],
            ],
        ];

        $this->userId = (string) $user->getId();
        $this->roleId = (string) $role->getId();
        $this->client->request(
            'DELETE',
            '/users/' . $this->userId . '/roles/' . $this->roleId,
            [],
            [],
            self::HEADERS
        );
        $response = $this->client->getResponse();
        $responseBody = json_decode((string) $response->getContent(), true);

        $this->assertEquals(Response::HTTP_NOT_FOUND, $response->getStatusCode());
        $this->assertEquals($expectedBody, $responseBody);

        $this->assertLogFailure(
            'users.roles.remove',
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
                    'code' => 's2.e602.c29',
                    'message' => 'user role not found',
                ]],
            ],
        ];

        $this->userId = (string) $user->getId();
        $this->roleId = (string) $role->getId();
        $this->client->request(
            'DELETE',
            '/users/' . $this->userId . '/roles/' . $this->roleId,
            [],
            [],
            self::HEADERS
        );
        $response = $this->client->getResponse();
        $responseBody = json_decode((string) $response->getContent(), true);

        $this->assertEquals(Response::HTTP_CONFLICT, $response->getStatusCode());
        $this->assertEquals($expectedBody, $responseBody);

        $this->assertLogFailure(
            'users.roles.remove',
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
                    'code' => 's2.e602.c29',
                    'message' => 'user role not found',
                ]],
            ],
        ];

        $this->userId = (string) $user->getId();
        $this->roleId = (string) $role->getId();
        $this->client->request(
            'DELETE',
            '/users/' . $this->userId . '/roles/' . $this->roleId,
            [],
            [],
            self::HEADERS
        );
        $response = $this->client->getResponse();
        $responseBody = json_decode((string) $response->getContent(), true);

        $this->assertEquals(Response::HTTP_CONFLICT, $response->getStatusCode());
        $this->assertEquals($expectedBody, $responseBody);

        $this->assertLogFailure(
            'users.roles.remove',
            Response::HTTP_CONFLICT,
            $expectedBody,
            [[$this, 'assertLogRequestValues']]
        );
    }

    /**
     * @return void
     * @throws Exception
     */
    public function testRelationCorrectlyRemoved(): void
    {
        $fixtures = new LocalFixtures();
        $fixtures->addUser(['email' => 'root@email.com']);
        $user = $fixtures->addUser();
        $role = $fixtures->addRole();
        $fixtures->addUserRole(['user' => $user, 'role' => $role]);
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
                    'name' => '__dummy_name__',
                    'description' => '__dummy_description__',
                    'priority' => 0,
                    'createdAt' => '2000-01-01 00:00:00',
                    'updatedAt' => '2000-01-01 00:00:00',
                    'deletedBy' => 'root@email.com',
                ]
            ]
        ];

        $this->userId = (string) $user->getId();
        $this->roleId = (string) $role->getId();
        $this->client->request(
            'DELETE',
            '/users/' . $this->userId . '/roles/' . $this->roleId,
            [],
            [],
            self::HEADERS
        );
        $response = $this->client->getResponse();
        $responseBody = json_decode((string) $response->getContent(), true);

        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
        $expectedBody['data']['role']['deletedAt'] = $responseBody['data']['role']['deletedAt'];
        $this->assertEquals($expectedBody, $responseBody);

        $rels = $this->localManager->getRepository(UserRole::class)->findAll();
        $this->assertCount(1, $rels);
        $this->assertNotNull($rels[0]->getDeletedAt());

        $this->assertLogSuccess(
            'users.roles.remove',
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
        }
    }
}
