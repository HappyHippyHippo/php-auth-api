<?php

namespace App\Tests\Flow\UserPermissions;

use App\Tests\Flow\EndpointTester;
use App\Tests\LocalFixtures;
use DateTime;
use Exception;
use Symfony\Component\HttpFoundation\Response;

class GetTest extends EndpointTester
{
    /** @var string */
    private string $userId;

    /** @var string */
    private string $permissionId;

    /**
     * @return void
     */
    public function testUserNotFound(): void
    {
        $expectedBody = [
            'status' => [
                'success' => false,
                'errors' => [['code' => 's2.e500.c10', 'message' => 'user not found']],
            ],
        ];

        $this->userId = "1";
        $this->permissionId = "1";
        $this->client->request(
            'GET',
            '/users/' . $this->userId . '/permissions/' . $this->permissionId,
            [],
            [],
            self::HEADERS
        );
        $response = $this->client->getResponse();
        $responseBody = json_decode((string) $response->getContent(), true);

        $this->assertEquals(Response::HTTP_NOT_FOUND, $response->getStatusCode());
        $this->assertEquals($expectedBody, $responseBody);

        $this->assertLogFailure(
            'user_permissions.get',
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
                'errors' => [['code' => 's2.e500.c10', 'message' => 'user not found']],
            ],
        ];

        $this->userId = "1";
        $this->permissionId = "1";
        $this->client->request(
            'GET',
            '/users/' . $this->userId . '/permissions/' . $this->permissionId,
            [],
            [],
            self::HEADERS
        );
        $response = $this->client->getResponse();
        $responseBody = json_decode((string) $response->getContent(), true);

        $this->assertEquals(Response::HTTP_NOT_FOUND, $response->getStatusCode());
        $this->assertEquals($expectedBody, $responseBody);

        $this->assertLogFailure(
            'user_permissions.get',
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
        $user = $fixtures->addUser();
        $this->loadFixtures($fixtures);

        $expectedBody = [
            'status' => [
                'success' => false,
                'errors' => [['code' => 's2.e500.c27', 'message' => 'user permission not found']],
            ],
        ];

        $this->userId = (string) $user->getId();
        $this->permissionId = "1";
        $this->client->request(
            'GET',
            '/users/' . $this->userId . '/permissions/' . $this->permissionId,
            [],
            [],
            self::HEADERS
        );
        $response = $this->client->getResponse();
        $responseBody = json_decode((string) $response->getContent(), true);

        $this->assertEquals(Response::HTTP_NOT_FOUND, $response->getStatusCode());
        $this->assertEquals($expectedBody, $responseBody);

        $this->assertLogFailure(
            'user_permissions.get',
            Response::HTTP_NOT_FOUND,
            $expectedBody,
            [[$this, 'assertLogRequestValues']]
        );
    }

    /**
     * @return void
     * @throws Exception
     */
    public function testPermissionFromDifferentUser(): void
    {
        $fixtures = new LocalFixtures();
        $user1 = $fixtures->addUser(['email' => '1.email@email.com']);
        $user2 = $fixtures->addUser(['email' => '2.email@email.com']);
        $perm1 = $fixtures->addUserPermission(['user' => $user1]);
        $this->loadFixtures($fixtures);

        $expectedBody = [
            'status' => [
                'success' => false,
                'errors' => [['code' => 's2.e500.c27', 'message' => 'user permission not found']],
            ],
        ];

        $this->userId = (string) $user2->getId();
        $this->permissionId = (string) $perm1->getId();
        $this->client->request(
            'GET',
            '/users/' . $this->userId . '/permissions/' . $this->permissionId,
            [],
            [],
            self::HEADERS
        );
        $response = $this->client->getResponse();
        $responseBody = json_decode((string) $response->getContent(), true);

        $this->assertEquals(Response::HTTP_NOT_FOUND, $response->getStatusCode());
        $this->assertEquals($expectedBody, $responseBody);

        $this->assertLogFailure(
            'user_permissions.get',
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
        $user = $fixtures->addUser();
        $perm = $fixtures->addUserPermission(['user' => $user, 'deletedAt' => new DateTime()]);
        $this->loadFixtures($fixtures);

        $expectedBody = [
            'status' => [
                'success' => false,
                'errors' => [['code' => 's2.e500.c27', 'message' => 'user permission not found']],
            ],
        ];

        $this->userId = (string) $user->getId();
        $this->permissionId = (string) $perm->getId();
        $this->client->request(
            'GET',
            '/users/' . $this->userId . '/permissions/' . $this->permissionId,
            [],
            [],
            self::HEADERS
        );
        $response = $this->client->getResponse();
        $responseBody = json_decode((string) $response->getContent(), true);

        $this->assertEquals(Response::HTTP_NOT_FOUND, $response->getStatusCode());
        $this->assertEquals($expectedBody, $responseBody);

        $this->assertLogFailure(
            'user_permissions.get',
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
        $user = $fixtures->addUser();
        $perm = $fixtures->addUserPermission(['user' => $user]);
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

        $this->userId = (string) $user->getId();
        $this->permissionId = (string) $perm->getId();
        $this->client->request(
            'GET',
            '/users/' . $this->userId . '/permissions/' . $this->permissionId,
            [],
            [],
            self::HEADERS
        );
        $response = $this->client->getResponse();
        $responseBody = json_decode((string) $response->getContent(), true);

        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
        $this->assertEquals($expectedBody, $responseBody);

        $this->assertLogSuccess(
            'user_permissions.get',
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
            $this->assertLogRequestAttr($record, 'permissionId', $this->permissionId);
        }
    }
}
