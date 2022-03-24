<?php

namespace App\Tests\Flow\UserPermissions;

use App\Model\Entity\Local\UserPermission;
use App\Tests\Flow\EndpointTester;
use App\Tests\LocalFixtures;
use DateTime;
use Exception;
use Symfony\Component\HttpFoundation\Response;

class EnableTest extends EndpointTester
{
    /** @var string */
    private const USER_ID = '2';

    /** @var string */
    private const PERMISSION_ID = '1';

    /**
     * @return void
     * @throws Exception
     */
    public function testUserNotFound(): void
    {
        $expectedBody = [
            'status' => [
                'success' => false,
                'errors' => [['code' => 's2.e504.c10', 'message' => 'user not found']],
            ],
        ];

        $this->client->request(
            'POST',
            '/users/' . self::USER_ID . '/permissions/' . self::PERMISSION_ID . '/enable',
            [],
            [],
            self::HEADERS
        );
        $response = $this->client->getResponse();
        $responseBody = json_decode((string) $response->getContent(), true);

        $this->assertEquals(Response::HTTP_NOT_FOUND, $response->getStatusCode());
        $this->assertEquals($expectedBody, $responseBody);

        $this->assertLogFailure(
            'user_permissions.enable',
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
        $fixtures->addUser(['email' => 'root@email.com']);
        $fixtures->addUser(['deletedAt' => new DateTime()]);
        $this->loadFixtures($fixtures);

        $expectedBody = [
            'status' => [
                'success' => false,
                'errors' => [['code' => 's2.e504.c10', 'message' => 'user not found']],
            ],
        ];

        $this->client->request(
            'POST',
            '/users/' . self::USER_ID . '/permissions/' . self::PERMISSION_ID . '/enable',
            [],
            [],
            self::HEADERS
        );
        $response = $this->client->getResponse();
        $responseBody = json_decode((string) $response->getContent(), true);

        $this->assertEquals(Response::HTTP_NOT_FOUND, $response->getStatusCode());
        $this->assertEquals($expectedBody, $responseBody);

        $this->assertLogFailure(
            'user_permissions.enable',
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
        $fixtures->addUser(['email' => 'root@email.com']);
        $fixtures->addUser();
        $this->loadFixtures($fixtures);

        $expectedBody = [
            'status' => [
                'success' => false,
                'errors' => [['code' => 's2.e504.c27', 'message' => 'user permission not found']],
            ],
        ];

        $this->client->request(
            'POST',
            '/users/' . self::USER_ID . '/permissions/' . self::PERMISSION_ID . '/enable',
            [],
            [],
            self::HEADERS
        );
        $response = $this->client->getResponse();
        $responseBody = json_decode((string) $response->getContent(), true);

        $this->assertEquals(Response::HTTP_NOT_FOUND, $response->getStatusCode());
        $this->assertEquals($expectedBody, $responseBody);

        $this->assertLogFailure(
            'user_permissions.enable',
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
        $fixtures->addUser(['email' => 'root@email.com']);
        $user = $fixtures->addUser();
        $fixtures->addUserPermission(['user' => $user, 'deletedAt' => new DateTime()]);
        $this->loadFixtures($fixtures);

        $expectedBody = [
            'status' => [
                'success' => false,
                'errors' => [['code' => 's2.e504.c27', 'message' => 'user permission not found']],
            ],
        ];

        $this->client->request(
            'POST',
            '/users/' . self::USER_ID . '/permissions/' . self::PERMISSION_ID . '/enable',
            [],
            [],
            self::HEADERS
        );
        $response = $this->client->getResponse();
        $responseBody = json_decode((string) $response->getContent(), true);

        $this->assertEquals(Response::HTTP_NOT_FOUND, $response->getStatusCode());
        $this->assertEquals($expectedBody, $responseBody);

        $this->assertLogFailure(
            'user_permissions.enable',
            Response::HTTP_NOT_FOUND,
            $expectedBody,
            [[$this, 'assertLogRequestValues']]
        );
    }

    /**
     * @return void
     * @throws Exception
     */
    public function testPermissionFromOtherUser(): void
    {
        $fixtures = new LocalFixtures();
        $fixtures->addUser(['email' => 'root@email.com']);
        $fixtures->addUser();
        $user = $fixtures->addUser(['email' => 'other@email.com']);
        $fixtures->addUserPermission(['user' => $user, 'deletedAt' => new DateTime()]);
        $this->loadFixtures($fixtures);

        $expectedBody = [
            'status' => [
                'success' => false,
                'errors' => [['code' => 's2.e504.c27', 'message' => 'user permission not found']],
            ],
        ];

        $this->client->request(
            'POST',
            '/users/' . self::USER_ID . '/permissions/' . self::PERMISSION_ID . '/enable',
            [],
            [],
            self::HEADERS
        );
        $response = $this->client->getResponse();
        $responseBody = json_decode((string) $response->getContent(), true);

        $this->assertEquals(Response::HTTP_NOT_FOUND, $response->getStatusCode());
        $this->assertEquals($expectedBody, $responseBody);

        $this->assertLogFailure(
            'user_permissions.enable',
            Response::HTTP_NOT_FOUND,
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
        $user = $fixtures->addUser();
        $fixtures->addUserPermission(['user' => $user, 'enabled' => false]);
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
                    'directory' => '__dummy_directory__',
                    'level' => 'none',
                    'description' => '__dummy_description__',
                    'updatedAt' => '1900-01-01 00:00:00',
                    'updatedBy' => 'root@email.com',
                ],
            ],
        ];

        $this->client->request(
            'POST',
            '/users/' . self::USER_ID . '/permissions/' . self::PERMISSION_ID . '/enable',
            [],
            [],
            self::HEADERS
        );
        $response = $this->client->getResponse();
        $responseBody = json_decode((string) $response->getContent(), true);

        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
        $expectedBody['data']['permission']['createdAt'] = $responseBody['data']['permission']['createdAt'];
        $expectedBody['data']['permission']['updatedAt'] = $responseBody['data']['permission']['updatedAt'];
        $this->assertEquals($expectedBody, $responseBody);

        $perms = $this->localManager->getRepository(UserPermission::class)->findAll();
        $this->assertCount(1, $perms);
        $this->assertTrue($perms[0]->isEnabled());
        $this->assertEquals(1, $perms[0]->getId());

        $this->assertLogSuccess(
            'user_permissions.enable',
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
            $this->assertLogRequestAttr($record, 'userId', self::USER_ID);
            $this->assertLogRequestAttr($record, 'permissionId', self::PERMISSION_ID);
            $this->assertLogRequestBody($record, []);
        }
    }
}
