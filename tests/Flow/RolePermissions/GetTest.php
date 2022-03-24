<?php

namespace App\Tests\Flow\RolePermissions;

use App\Tests\Flow\EndpointTester;
use App\Tests\LocalFixtures;
use DateTime;
use Exception;
use Symfony\Component\HttpFoundation\Response;

class GetTest extends EndpointTester
{
    /** @var string */
    private string $roleId;

    /** @var string */
    private string $permissionId;

    /**
     * @return void
     */
    public function testRoleNotFound(): void
    {
        $expectedBody = [
            'status' => [
                'success' => false,
                'errors' => [['code' => 's2.e400.c23', 'message' => 'role not found']],
            ],
        ];

        $this->roleId = "1";
        $this->permissionId = "1";
        $this->client->request(
            'GET',
            '/roles/' . $this->roleId . '/permissions/' . $this->permissionId,
            [],
            [],
            self::HEADERS
        );
        $response = $this->client->getResponse();
        $responseBody = json_decode((string) $response->getContent(), true);

        $this->assertEquals(Response::HTTP_NOT_FOUND, $response->getStatusCode());
        $this->assertEquals($expectedBody, $responseBody);

        $this->assertLogFailure(
            'role_permissions.get',
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
                'errors' => [['code' => 's2.e400.c23', 'message' => 'role not found']],
            ],
        ];

        $this->roleId = "1";
        $this->permissionId = "1";
        $this->client->request(
            'GET',
            '/roles/' . $this->roleId . '/permissions/' . $this->permissionId,
            [],
            [],
            self::HEADERS
        );
        $response = $this->client->getResponse();
        $responseBody = json_decode((string) $response->getContent(), true);

        $this->assertEquals(Response::HTTP_NOT_FOUND, $response->getStatusCode());
        $this->assertEquals($expectedBody, $responseBody);

        $this->assertLogFailure(
            'role_permissions.get',
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
        $role = $fixtures->addRole();
        $this->loadFixtures($fixtures);

        $expectedBody = [
            'status' => [
                'success' => false,
                'errors' => [['code' => 's2.e400.c25', 'message' => 'role permission not found']],
            ],
        ];

        $this->roleId = (string) $role->getId();
        $this->permissionId = "1";
        $this->client->request(
            'GET',
            '/roles/' . $this->roleId . '/permissions/' . $this->permissionId,
            [],
            [],
            self::HEADERS
        );
        $response = $this->client->getResponse();
        $responseBody = json_decode((string) $response->getContent(), true);

        $this->assertEquals(Response::HTTP_NOT_FOUND, $response->getStatusCode());
        $this->assertEquals($expectedBody, $responseBody);

        $this->assertLogFailure(
            'role_permissions.get',
            Response::HTTP_NOT_FOUND,
            $expectedBody,
            [[$this, 'assertLogRequestValues']]
        );
    }

    /**
     * @return void
     * @throws Exception
     */
    public function testPermissionFromDifferentRole(): void
    {
        $fixtures = new LocalFixtures();
        $role1 = $fixtures->addRole(['name' => '__dummy_name_1__']);
        $role2 = $fixtures->addRole(['name' => '__dummy_name_2__']);
        $perm1 = $fixtures->addRolePermission(['role' => $role1]);
        $this->loadFixtures($fixtures);

        $expectedBody = [
            'status' => [
                'success' => false,
                'errors' => [['code' => 's2.e400.c25', 'message' => 'role permission not found']],
            ],
        ];

        $this->roleId = (string) $role2->getId();
        $this->permissionId = (string) $perm1->getId();
        $this->client->request(
            'GET',
            '/roles/' . $this->roleId . '/permissions/' . $this->permissionId,
            [],
            [],
            self::HEADERS
        );
        $response = $this->client->getResponse();
        $responseBody = json_decode((string) $response->getContent(), true);

        $this->assertEquals(Response::HTTP_NOT_FOUND, $response->getStatusCode());
        $this->assertEquals($expectedBody, $responseBody);

        $this->assertLogFailure(
            'role_permissions.get',
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
        $perm = $fixtures->addRolePermission(['role' => $role, 'deletedAt' => new DateTime()]);
        $this->loadFixtures($fixtures);

        $expectedBody = [
            'status' => [
                'success' => false,
                'errors' => [['code' => 's2.e400.c25', 'message' => 'role permission not found']],
            ],
        ];

        $this->roleId = (string) $role->getId();
        $this->permissionId = (string) $perm->getId();
        $this->client->request(
            'GET',
            '/roles/' . $this->roleId . '/permissions/' . $this->permissionId,
            [],
            [],
            self::HEADERS
        );
        $response = $this->client->getResponse();
        $responseBody = json_decode((string) $response->getContent(), true);

        $this->assertEquals(Response::HTTP_NOT_FOUND, $response->getStatusCode());
        $this->assertEquals($expectedBody, $responseBody);

        $this->assertLogFailure(
            'role_permissions.get',
            Response::HTTP_NOT_FOUND,
            $expectedBody,
            [[$this, 'assertLogRequestValues']]
        );
    }

    /**
     * @return void
     * @throws Exception
     */
    public function testSuccess(): void
    {
        $fixtures = new LocalFixtures();
        $role = $fixtures->addRole();
        $perm = $fixtures->addRolePermission(['role' => $role]);
        $this->loadFixtures($fixtures);

        $expectedBody = [
            'status' => [
                'success' => true,
                'errors' => [],
            ],
            'data' => [
                'permission' => $perm->jsonSerialize(),
            ],
        ];

        $this->roleId = (string) $role->getId();
        $this->permissionId = (string) $perm->getId();
        $this->client->request(
            'GET',
            '/roles/' . $this->roleId . '/permissions/' . $this->permissionId,
            [],
            [],
            self::HEADERS
        );
        $response = $this->client->getResponse();
        $responseBody = json_decode((string) $response->getContent(), true);

        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
        $this->assertEquals($expectedBody, $responseBody);

        $this->assertLogSuccess(
            'role_permissions.get',
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
            $this->assertLogRequestAttr($record, 'roleId', $this->roleId);
            $this->assertLogRequestAttr($record, 'permissionId', $this->permissionId);
        }
    }
}
