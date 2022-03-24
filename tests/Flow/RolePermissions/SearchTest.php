<?php

namespace App\Tests\Flow\RolePermissions;

use App\Tests\Flow\EndpointTester;
use App\Tests\LocalFixtures;
use DateTime;
use Exception;
use Symfony\Component\HttpFoundation\Response;

class SearchTest extends EndpointTester
{
    /**
     * @return void
     * @throws Exception
     */
    public function testRoleNotFound(): void
    {
        $expectedBody = [
            'status' => [
                'success' => false,
                'errors' => [['code' => 's2.e401.c23', 'message' => 'role not found']],
            ],
        ];

        $this->client->request('GET', '/roles/1/permissions', [], [], self::HEADERS);
        $response = $this->client->getResponse();
        $responseBody = json_decode((string) $response->getContent(), true);

        $this->assertEquals(Response::HTTP_NOT_FOUND, $response->getStatusCode());
        $this->assertEquals($expectedBody, $responseBody);

        $this->assertLogFailure('role_permissions.search', Response::HTTP_NOT_FOUND, $expectedBody);
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
                'errors' => [['code' => 's2.e401.c23', 'message' => 'role not found']],
            ],
        ];

        $this->client->request('GET', '/roles/1/permissions', [], [], self::HEADERS);
        $response = $this->client->getResponse();
        $responseBody = json_decode((string) $response->getContent(), true);

        $this->assertEquals(Response::HTTP_NOT_FOUND, $response->getStatusCode());
        $this->assertEquals($expectedBody, $responseBody);

        $this->assertLogFailure('role_permissions.search', Response::HTTP_NOT_FOUND, $expectedBody);
    }

    /**
     * @return void
     * @throws Exception
     */
    public function testSearchListAll(): void
    {
        $fixtures = new LocalFixtures();
        $role1 = $fixtures->addRole(['name' => '__dummy_name_1__']);
        $role2 = $fixtures->addRole(['name' => '__dummy_name_2__']);
        $perm1 = $fixtures->addRolePermission(['role' => $role1, 'directory' => '__dummy_directory_1__']);
        $perm2 = $fixtures->addRolePermission(['role' => $role1, 'directory' => '__dummy_directory_2__']);
        $perm3 = $fixtures->addRolePermission(['role' => $role1, 'directory' => '__dummy_directory_3__']);
        $fixtures->addRolePermission(['role' => $role2, 'directory' => '__dummy_directory_1__']);
        $fixtures->addRolePermission(['role' => $role2, 'directory' => '__dummy_directory_2__']);
        $fixtures->addRolePermission(['role' => $role2, 'directory' => '__dummy_directory_3__']);
        $this->loadFixtures($fixtures);

        $expectedBody = [
            'status' => ['success' => true, 'errors' => []],
            'data' => [
                'permissions' => [
                    1 => $perm1->jsonSerialize(),
                    2 => $perm2->jsonSerialize(),
                    3 => $perm3->jsonSerialize(),
                ],
                'report' => [
                    'search' => '',
                    'start' => 0,
                    'count' => 50,
                    'total' => 3,
                    'prev' => '',
                    'next' => ''
                ],
            ],
        ];

        $this->client->request('GET', '/roles/' . $role1->getId() . '/permissions', [], [], self::HEADERS);
        $response = $this->client->getResponse();
        $responseBody = json_decode((string) $response->getContent(), true);

        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
        $this->assertEquals($expectedBody, $responseBody);

        $this->assertLogSuccess('role_permissions.search', Response::HTTP_OK);
    }

    /**
     * @return void
     * @throws Exception
     */
    public function testSearchByDirectory(): void
    {
        $fixtures = new LocalFixtures();
        $role1 = $fixtures->addRole(['name' => '__dummy_name_1__']);
        $role2 = $fixtures->addRole(['name' => '__dummy_name_2__']);
        $fixtures->addRolePermission(['role' => $role1, 'directory' => '__dummy_directory_1__']);
        $perm2 = $fixtures->addRolePermission(['role' => $role1, 'directory' => '__dummy_directory_2__']);
        $fixtures->addRolePermission(['role' => $role1, 'directory' => '__dummy_directory_3__']);
        $fixtures->addRolePermission(['role' => $role2, 'directory' => '__dummy_directory_1__']);
        $fixtures->addRolePermission(['role' => $role2, 'directory' => '__dummy_directory_2__']);
        $fixtures->addRolePermission(['role' => $role2, 'directory' => '__dummy_directory_3__']);
        $this->loadFixtures($fixtures);

        $expectedBody = [
            'status' => ['success' => true, 'errors' => []],
            'data' => [
                'permissions' => [
                    2 => $perm2->jsonSerialize(),
                ],
                'report' => [
                    'search' => 'directory_2',
                    'start' => 0,
                    'count' => 50,
                    'total' => 1,
                    'prev' => '',
                    'next' => ''
                ],
            ],
        ];

        $this->client->request(
            'GET',
            '/roles/' . $role1->getId() . '/permissions',
            ['search' => 'directory_2'],
            [],
            self::HEADERS
        );
        $response = $this->client->getResponse();
        $responseBody = json_decode((string) $response->getContent(), true);

        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
        $this->assertEquals($expectedBody, $responseBody);

        $this->assertLogSuccess('role_permissions.search', Response::HTTP_OK);
    }

    /**
     * @return void
     * @throws Exception
     */
    public function testSearchWithOffset(): void
    {
        $fixtures = new LocalFixtures();
        $role1 = $fixtures->addRole(['name' => '__dummy_name_1__']);
        $role2 = $fixtures->addRole(['name' => '__dummy_name_2__']);
        $fixtures->addRolePermission(['role' => $role1, 'directory' => '__dummy_directory_1__']);
        $perm2 = $fixtures->addRolePermission(['role' => $role1, 'directory' => '__dummy_directory_2__']);
        $perm3 = $fixtures->addRolePermission(['role' => $role1, 'directory' => '__dummy_directory_3__']);
        $fixtures->addRolePermission(['role' => $role2, 'directory' => '__dummy_directory_1__']);
        $fixtures->addRolePermission(['role' => $role2, 'directory' => '__dummy_directory_2__']);
        $fixtures->addRolePermission(['role' => $role2, 'directory' => '__dummy_directory_3__']);
        $this->loadFixtures($fixtures);

        $expectedBody = [
            'status' => ['success' => true, 'errors' => []],
            'data' => [
                'permissions' => [
                    2 => $perm2->jsonSerialize(),
                    3 => $perm3->jsonSerialize(),
                ],
                'report' => [
                    'search' => '',
                    'start' => 1,
                    'count' => 50,
                    'total' => 3,
                    'prev' => '?search=&start=0&count=50',
                    'next' => ''
                ],
            ],
        ];

        $this->client->request(
            'GET',
            '/roles/' . $role1->getId() . '/permissions',
            ['start' => 1],
            [],
            self::HEADERS
        );
        $response = $this->client->getResponse();
        $responseBody = json_decode((string) $response->getContent(), true);

        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
        $this->assertEquals($expectedBody, $responseBody);

        $this->assertLogSuccess('role_permissions.search', Response::HTTP_OK);
    }

    /**
     * @return void
     * @throws Exception
     */
    public function testSearchWithOffsetAndLimit(): void
    {
        $fixtures = new LocalFixtures();
        $role1 = $fixtures->addRole(['name' => '__dummy_name_1__']);
        $role2 = $fixtures->addRole(['name' => '__dummy_name_2__']);
        $fixtures->addRolePermission(['role' => $role1, 'directory' => '__dummy_directory_1__']);
        $perm2 = $fixtures->addRolePermission(['role' => $role1, 'directory' => '__dummy_directory_2__']);
        $fixtures->addRolePermission(['role' => $role1, 'directory' => '__dummy_directory_3__']);
        $fixtures->addRolePermission(['role' => $role2, 'directory' => '__dummy_directory_1__']);
        $fixtures->addRolePermission(['role' => $role2, 'directory' => '__dummy_directory_2__']);
        $fixtures->addRolePermission(['role' => $role2, 'directory' => '__dummy_directory_3__']);
        $this->loadFixtures($fixtures);

        $expectedBody = [
            'status' => ['success' => true, 'errors' => []],
            'data' => [
                'permissions' => [
                    2 => $perm2->jsonSerialize(),
                ],
                'report' => [
                    'search' => '',
                    'start' => 1,
                    'count' => 1,
                    'total' => 3,
                    'prev' => '?search=&start=0&count=1',
                    'next' => '?search=&start=2&count=1'
                ],
            ],
        ];

        $this->client->request(
            'GET',
            '/roles/' . $role1->getId() . '/permissions',
            ['start' => 1, 'count' => 1],
            [],
            self::HEADERS
        );
        $response = $this->client->getResponse();
        $responseBody = json_decode((string) $response->getContent(), true);

        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
        $this->assertEquals($expectedBody, $responseBody);

        $this->assertLogSuccess('role_permissions.search', Response::HTTP_OK);
    }
}
