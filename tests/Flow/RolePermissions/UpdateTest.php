<?php

namespace App\Tests\Flow\RolePermissions;

use App\Model\Entity\Local\PermissionLevel;
use App\Model\Entity\Local\RolePermission;
use App\Tests\Flow\EndpointTester;
use App\Tests\LocalFixtures;
use DateTime;
use Exception;
use Symfony\Component\HttpFoundation\Response;

class UpdateTest extends EndpointTester
{
    /** @var string */
    private const ROLE_ID = '1';

    /** @var string */
    private const PERMISSION_ID = '1';

    /** @var string */
    private const DIRECTORY = '__dummy_directory__';

    /** @var string */
    private const LEVEL = 'self';

    /** @var string */
    private const DESCRIPTION = '__dummy_description__';

    /** @var array<string, mixed> */
    private const PARAMS = [
        'enabled' => true,
        'directory' => self::DIRECTORY,
        'level' => self::LEVEL,
        'description' => self::DESCRIPTION,
    ];

    /**
     * @return void
     * @throws Exception
     */
    public function testRoleNotFound(): void
    {
        $expectedBody = [
            'status' => [
                'success' => false,
                'errors' => [['code' => 's2.e403.c23', 'message' => 'role not found']],
            ],
        ];

        $this->client->request(
            'POST',
            '/roles/' . self::ROLE_ID . '/permissions/' . self::PERMISSION_ID,
            self::PARAMS,
            [],
            self::HEADERS
        );
        $response = $this->client->getResponse();
        $responseBody = json_decode((string) $response->getContent(), true);

        $this->assertEquals(Response::HTTP_NOT_FOUND, $response->getStatusCode());
        $this->assertEquals($expectedBody, $responseBody);

        $this->assertLogFailure(
            'role_permissions.update',
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
        $fixtures->addRole(['deletedAt' => new DateTime()]);
        $this->loadFixtures($fixtures);

        $expectedBody = [
            'status' => [
                'success' => false,
                'errors' => [['code' => 's2.e403.c23', 'message' => 'role not found']],
            ],
        ];

        $this->client->request(
            'POST',
            '/roles/' . self::ROLE_ID . '/permissions/' . self::PERMISSION_ID,
            self::PARAMS,
            [],
            self::HEADERS
        );
        $response = $this->client->getResponse();
        $responseBody = json_decode((string) $response->getContent(), true);

        $this->assertEquals(Response::HTTP_NOT_FOUND, $response->getStatusCode());
        $this->assertEquals($expectedBody, $responseBody);

        $this->assertLogFailure(
            'role_permissions.update',
            Response::HTTP_NOT_FOUND,
            $expectedBody,
            [[$this, 'assertLogRequestValues']]
        );
    }

    /**
     * @return void
     * @throws Exception
     */
    public function testPermissionNotFound(): void
    {
        $fixtures = new LocalFixtures();
        $fixtures->addRole();
        $this->loadFixtures($fixtures);

        $expectedBody = [
            'status' => [
                'success' => false,
                'errors' => [['code' => 's2.e403.c25', 'message' => 'role permission not found']],
            ],
        ];

        $this->client->request(
            'POST',
            '/roles/' . self::ROLE_ID . '/permissions/' . self::PERMISSION_ID,
            self::PARAMS,
            [],
            self::HEADERS
        );
        $response = $this->client->getResponse();
        $responseBody = json_decode((string) $response->getContent(), true);

        $this->assertEquals(Response::HTTP_NOT_FOUND, $response->getStatusCode());
        $this->assertEquals($expectedBody, $responseBody);

        $this->assertLogFailure(
            'role_permissions.update',
            Response::HTTP_NOT_FOUND,
            $expectedBody,
            [[$this, 'assertLogRequestValues']]
        );
    }

    /**
     * @return void
     * @throws Exception
     */
    public function testPermissionDeleted(): void
    {
        $fixtures = new LocalFixtures();
        $role = $fixtures->addRole();
        $fixtures->addRolePermission(['role' => $role, 'deletedAt' => new DateTime()]);
        $this->loadFixtures($fixtures);

        $expectedBody = [
            'status' => [
                'success' => false,
                'errors' => [['code' => 's2.e403.c25', 'message' => 'role permission not found']],
            ],
        ];

        $this->client->request(
            'POST',
            '/roles/' . self::ROLE_ID . '/permissions/' . self::PERMISSION_ID,
            self::PARAMS,
            [],
            self::HEADERS
        );
        $response = $this->client->getResponse();
        $responseBody = json_decode((string) $response->getContent(), true);

        $this->assertEquals(Response::HTTP_NOT_FOUND, $response->getStatusCode());
        $this->assertEquals($expectedBody, $responseBody);

        $this->assertLogFailure(
            'role_permissions.update',
            Response::HTTP_NOT_FOUND,
            $expectedBody,
            [[$this, 'assertLogRequestValues']]
        );
    }

    /**
     * @return void
     * @throws Exception
     */
    public function testPermissionFromOtherRole(): void
    {
        $fixtures = new LocalFixtures();
        $fixtures->addRole();
        $role = $fixtures->addRole(['name' => 'dummy_other_name__']);
        $fixtures->addRolePermission(['role' => $role, 'deletedAt' => new DateTime()]);
        $this->loadFixtures($fixtures);

        $expectedBody = [
            'status' => [
                'success' => false,
                'errors' => [['code' => 's2.e403.c25', 'message' => 'role permission not found']],
            ],
        ];

        $this->client->request(
            'POST',
            '/roles/' . self::ROLE_ID . '/permissions/' . self::PERMISSION_ID,
            self::PARAMS,
            [],
            self::HEADERS
        );
        $response = $this->client->getResponse();
        $responseBody = json_decode((string) $response->getContent(), true);

        $this->assertEquals(Response::HTTP_NOT_FOUND, $response->getStatusCode());
        $this->assertEquals($expectedBody, $responseBody);

        $this->assertLogFailure(
            'role_permissions.update',
            Response::HTTP_NOT_FOUND,
            $expectedBody,
            [[$this, 'assertLogRequestValues']]
        );
    }

    /**
     * @return void
     * @throws Exception
     */
    public function testDuplicateDirectory(): void
    {
        $fixtures = new LocalFixtures();
        $role = $fixtures->addRole();
        $fixtures->addRolePermission(['role' => $role, 'directory' => '__dummy_other_directory__']);
        $fixtures->addRolePermission(['role' => $role, 'directory' => self::DIRECTORY]);
        $this->loadFixtures($fixtures);

        $expectedBody = [
            'status' => [
                'success' => false,
                'errors' => [[
                    'code' => 's2.e403.c26',
                    'message' => 'role permission directory already existent in the database'
                ]],
            ],
        ];

        $this->client->request(
            'POST',
            '/roles/' . self::ROLE_ID . '/permissions/' . self::PERMISSION_ID,
            self::PARAMS,
            [],
            self::HEADERS
        );
        $response = $this->client->getResponse();
        $responseBody = json_decode((string) $response->getContent(), true);

        $this->assertEquals(Response::HTTP_CONFLICT, $response->getStatusCode());
        $this->assertEquals($expectedBody, $responseBody);

        $this->assertLogFailure(
            'role_permissions.update',
            Response::HTTP_CONFLICT,
            $expectedBody,
            [[$this, 'assertLogRequestValues']]
        );
    }

    /**
     * @return void
     * @throws Exception
     */
    public function testUpdatePermission(): void
    {
        $fixtures = new LocalFixtures();
        $fixtures->addUser(['email' => 'root@email.com']);
        $role = $fixtures->addRole();
        $fixtures->addRolePermission([
            'role' => $role,
            'enabled' => false,
            'directory' => '__dummy_other_directory__',
            'level' => PermissionLevel::ALL,
            'description' => '__dummy_other_description__',
        ]);
        $this->loadFixtures($fixtures);

        $expectedBody = [
            'status' => [
                'success' => true,
                'errors' => [],
            ],
            'data' => [
                'permission' => [
                    'id' => 1,
                    'enabled' => true,
                    'directory' => self::DIRECTORY,
                    'level' => self::LEVEL,
                    'description' => self::DESCRIPTION,
                    'updatedAt' => '1900-01-01 00:00:00',
                    'updatedBy' => 'root@email.com',
                ],
            ],
        ];

        $this->client->request(
            'POST',
            '/roles/' . self::ROLE_ID . '/permissions/' . self::PERMISSION_ID,
            self::PARAMS,
            [],
            self::HEADERS
        );
        $response = $this->client->getResponse();
        $responseBody = json_decode((string) $response->getContent(), true);

        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
        $expectedBody['data']['permission']['createdAt'] = $responseBody['data']['permission']['createdAt'];
        $expectedBody['data']['permission']['updatedAt'] = $responseBody['data']['permission']['updatedAt'];
        $this->assertEquals($expectedBody, $responseBody);

        $perms = $this->localManager->getRepository(RolePermission::class)->findAll();
        $this->assertCount(1, $perms);
        $this->assertTrue($perms[0]->isEnabled());
        $this->assertEquals(1, $perms[0]->getId());
        $this->assertEquals(self::DIRECTORY, $perms[0]->getDirectory());
        $this->assertEquals(self::LEVEL, $perms[0]->getLevel()?->value);
        $this->assertEquals(self::DESCRIPTION, $perms[0]->getDescription());

        $this->assertLogSuccess(
            'role_permissions.update',
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
            $this->assertLogRequestAttr($record, 'roleId', self::ROLE_ID);
            $this->assertLogRequestAttr($record, 'permissionId', self::PERMISSION_ID);
            $this->assertLogRequestBody($record, self::PARAMS);
        }
    }
}
