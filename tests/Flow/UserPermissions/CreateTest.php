<?php

namespace App\Tests\Flow\UserPermissions;

use App\Model\Entity\Local\UserPermission;
use App\Tests\Flow\EndpointTester;
use App\Tests\LocalFixtures;
use DateTime;
use Exception;
use Symfony\Component\HttpFoundation\Response;

class CreateTest extends EndpointTester
{
    /** @var string */
    private const USER_ID = '1';

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
    public function testUserNotFound(): void
    {
        $expectedBody = [
            'status' => [
                'success' => false,
                'errors' => [['code' => 's2.e502.c10', 'message' => 'user not found']],
            ],
        ];

        $this->client->request(
            'POST',
            '/users/' . self::USER_ID . '/permissions',
            self::PARAMS,
            [],
            self::HEADERS
        );
        $response = $this->client->getResponse();
        $responseBody = json_decode((string) $response->getContent(), true);

        $this->assertEquals(Response::HTTP_NOT_FOUND, $response->getStatusCode());
        $this->assertEquals($expectedBody, $responseBody);

        $perms = $this->localManager->getRepository(UserPermission::class)->findAll();
        $this->assertCount(0, $perms);

        $this->assertLogFailure(
            'user_permissions.create',
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
                'errors' => [['code' => 's2.e502.c10', 'message' => 'user not found']],
            ],
        ];

        $this->client->request(
            'POST',
            '/users/' . self::USER_ID . '/permissions',
            self::PARAMS,
            [],
            self::HEADERS
        );
        $response = $this->client->getResponse();
        $responseBody = json_decode((string) $response->getContent(), true);

        $this->assertEquals(Response::HTTP_NOT_FOUND, $response->getStatusCode());
        $this->assertEquals($expectedBody, $responseBody);

        $perms = $this->localManager->getRepository(UserPermission::class)->findAll();
        $this->assertCount(0, $perms);

        $this->assertLogFailure(
            'user_permissions.create',
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
        $user = $fixtures->addUser();
        $fixtures->addUserPermission(['user' => $user, 'directory' => self::DIRECTORY]);
        $this->loadFixtures($fixtures);

        $expectedBody = [
            'status' => [
                'success' => false,
                'errors' => [[
                    'code' => 's2.e502.c28',
                    'message' => 'user permission directory already existent in the database'
                ]],
            ],
        ];

        $this->client->request(
            'POST',
            '/users/' . self::USER_ID . '/permissions',
            self::PARAMS,
            [],
            self::HEADERS
        );
        $response = $this->client->getResponse();
        $responseBody = json_decode((string) $response->getContent(), true);

        $this->assertEquals(Response::HTTP_CONFLICT, $response->getStatusCode());
        $this->assertEquals($expectedBody, $responseBody);

        $perms = $this->localManager->getRepository(UserPermission::class)->findAll();
        $this->assertCount(1, $perms);

        $this->assertLogFailure(
            'user_permissions.create',
            Response::HTTP_CONFLICT,
            $expectedBody,
            [[$this, 'assertLogRequestValues']]
        );
    }

    /**
     * @return void
     * @throws Exception
     */
    public function testCreatePermission(): void
    {
        $fixtures = new LocalFixtures();
        $fixtures->addUser(['email' => 'root@email.com']);
        $fixtures->addUser();
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
                    'createdAt' => '1900-01-01 00:00:00',
                    'createdBy' => 'root@email.com',
                    'updatedAt' => '1900-01-01 00:00:00',
                    'updatedBy' => 'root@email.com',
                ],
            ],
        ];

        $this->client->request(
            'POST',
            '/users/' . self::USER_ID . '/permissions',
            self::PARAMS,
            [],
            self::HEADERS
        );
        $response = $this->client->getResponse();
        $responseBody = json_decode((string) $response->getContent(), true);

        $this->assertEquals(Response::HTTP_CREATED, $response->getStatusCode());
        $expectedBody['data']['permission']['createdAt'] = $responseBody['data']['permission']['createdAt'];
        $expectedBody['data']['permission']['updatedAt'] = $responseBody['data']['permission']['updatedAt'];
        $this->assertEquals($expectedBody, $responseBody);

        $perms = $this->localManager->getRepository(UserPermission::class)->findAll();
        $this->assertCount(1, $perms);
        $this->assertTrue($perms[0]->isEnabled());
        $this->assertEquals(1, $perms[0]->getId());
        $this->assertEquals(self::DIRECTORY, $perms[0]->getDirectory());
        $this->assertEquals(self::LEVEL, $perms[0]->getLevel()?->value);
        $this->assertEquals(self::DESCRIPTION, $perms[0]->getDescription());

        $this->assertLogSuccess(
            'user_permissions.create',
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
            $this->assertLogRequestAttr($record, 'userId', self::USER_ID);
            $this->assertLogRequestBody($record, self::PARAMS);
        }
    }
}
