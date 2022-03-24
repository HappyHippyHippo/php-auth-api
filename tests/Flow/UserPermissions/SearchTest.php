<?php

namespace App\Tests\Flow\UserPermissions;

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
    public function testUserNotFound(): void
    {
        $expectedBody = [
            'status' => [
                'success' => false,
                'errors' => [['code' => 's2.e501.c10', 'message' => 'user not found']],
            ],
        ];

        $this->client->request('GET', '/users/1/permissions', [], [], self::HEADERS);
        $response = $this->client->getResponse();
        $responseBody = json_decode((string) $response->getContent(), true);

        $this->assertEquals(Response::HTTP_NOT_FOUND, $response->getStatusCode());
        $this->assertEquals($expectedBody, $responseBody);

        $this->assertLogFailure('user_permissions.search', Response::HTTP_NOT_FOUND, $expectedBody);
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
                'errors' => [['code' => 's2.e501.c10', 'message' => 'user not found']],
            ],
        ];

        $this->client->request('GET', '/users/1/permissions', [], [], self::HEADERS);
        $response = $this->client->getResponse();
        $responseBody = json_decode((string) $response->getContent(), true);

        $this->assertEquals(Response::HTTP_NOT_FOUND, $response->getStatusCode());
        $this->assertEquals($expectedBody, $responseBody);

        $this->assertLogFailure('user_permissions.search', Response::HTTP_NOT_FOUND, $expectedBody);
    }

    /**
     * @return void
     * @throws Exception
     */
    public function testSearchListAll(): void
    {
        $fixtures = new LocalFixtures();
        $user1 = $fixtures->addUser(['email' => '1.email@email.com']);
        $user2 = $fixtures->addUser(['email' => '2.email@email.com']);
        $perm1 = $fixtures->addUserPermission(['user' => $user1, 'directory' => '__dummy_directory_1__']);
        $perm2 = $fixtures->addUserPermission(['user' => $user1, 'directory' => '__dummy_directory_2__']);
        $perm3 = $fixtures->addUserPermission(['user' => $user1, 'directory' => '__dummy_directory_3__']);
        $fixtures->addUserPermission(['user' => $user2, 'directory' => '__dummy_directory_1__']);
        $fixtures->addUserPermission(['user' => $user2, 'directory' => '__dummy_directory_2__']);
        $fixtures->addUserPermission(['user' => $user2, 'directory' => '__dummy_directory_3__']);
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

        $this->client->request('GET', '/users/' . $user1->getId() . '/permissions', [], [], self::HEADERS);
        $response = $this->client->getResponse();
        $responseBody = json_decode((string) $response->getContent(), true);

        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
        $this->assertEquals($expectedBody, $responseBody);

        $this->assertLogSuccess('user_permissions.search', Response::HTTP_OK);
    }

    /**
     * @return void
     * @throws Exception
     */
    public function testSearchByDirectory(): void
    {
        $fixtures = new LocalFixtures();
        $user1 = $fixtures->addUser(['email' => '1.email@email.com']);
        $user2 = $fixtures->addUser(['email' => '2.email@email.com']);
        $fixtures->addUserPermission(['user' => $user1, 'directory' => '__dummy_directory_1__']);
        $perm2 = $fixtures->addUserPermission(['user' => $user1, 'directory' => '__dummy_directory_2__']);
        $fixtures->addUserPermission(['user' => $user1, 'directory' => '__dummy_directory_3__']);
        $fixtures->addUserPermission(['user' => $user2, 'directory' => '__dummy_directory_1__']);
        $fixtures->addUserPermission(['user' => $user2, 'directory' => '__dummy_directory_2__']);
        $fixtures->addUserPermission(['user' => $user2, 'directory' => '__dummy_directory_3__']);
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
            '/users/' . $user1->getId() . '/permissions',
            ['search' => 'directory_2'],
            [],
            self::HEADERS
        );
        $response = $this->client->getResponse();
        $responseBody = json_decode((string) $response->getContent(), true);

        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
        $this->assertEquals($expectedBody, $responseBody);

        $this->assertLogSuccess('user_permissions.search', Response::HTTP_OK);
    }

    /**
     * @return void
     * @throws Exception
     */
    public function testSearchWithOffset(): void
    {
        $fixtures = new LocalFixtures();
        $user1 = $fixtures->addUser(['email' => '1.email@email.com']);
        $user2 = $fixtures->addUser(['email' => '2.email@email.com']);
        $fixtures->addUserPermission(['user' => $user1, 'directory' => '__dummy_directory_1__']);
        $perm2 = $fixtures->addUserPermission(['user' => $user1, 'directory' => '__dummy_directory_2__']);
        $perm3 = $fixtures->addUserPermission(['user' => $user1, 'directory' => '__dummy_directory_3__']);
        $fixtures->addUserPermission(['user' => $user2, 'directory' => '__dummy_directory_1__']);
        $fixtures->addUserPermission(['user' => $user2, 'directory' => '__dummy_directory_2__']);
        $fixtures->addUserPermission(['user' => $user2, 'directory' => '__dummy_directory_3__']);
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
            '/users/' . $user1->getId() . '/permissions',
            ['start' => 1],
            [],
            self::HEADERS
        );
        $response = $this->client->getResponse();
        $responseBody = json_decode((string) $response->getContent(), true);

        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
        $this->assertEquals($expectedBody, $responseBody);

        $this->assertLogSuccess('user_permissions.search', Response::HTTP_OK);
    }

    /**
     * @return void
     * @throws Exception
     */
    public function testSearchWithOffsetAndLimit(): void
    {
        $fixtures = new LocalFixtures();
        $user1 = $fixtures->addUser(['email' => '1.email@email.com']);
        $user2 = $fixtures->addUser(['email' => '2.email@email.com']);
        $fixtures->addUserPermission(['user' => $user1, 'directory' => '__dummy_directory_1__']);
        $perm2 = $fixtures->addUserPermission(['user' => $user1, 'directory' => '__dummy_directory_2__']);
        $fixtures->addUserPermission(['user' => $user1, 'directory' => '__dummy_directory_3__']);
        $fixtures->addUserPermission(['user' => $user2, 'directory' => '__dummy_directory_1__']);
        $fixtures->addUserPermission(['user' => $user2, 'directory' => '__dummy_directory_2__']);
        $fixtures->addUserPermission(['user' => $user2, 'directory' => '__dummy_directory_3__']);
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
            '/users/' . $user1->getId() . '/permissions',
            ['start' => 1, 'count' => 1],
            [],
            self::HEADERS
        );
        $response = $this->client->getResponse();
        $responseBody = json_decode((string) $response->getContent(), true);

        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
        $this->assertEquals($expectedBody, $responseBody);

        $this->assertLogSuccess('user_permissions.search', Response::HTTP_OK);
    }
}
