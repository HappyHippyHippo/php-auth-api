<?php

namespace App\Tests\Flow\Roles;

use App\Model\Entity\Local\Role;
use App\Tests\Flow\EndpointTester;
use App\Tests\LocalFixtures;
use DateTime;
use Exception;
use Symfony\Component\HttpFoundation\Response;

class UpdateTest extends EndpointTester
{
    /** @var string */
    private const NAME = '__dummy_name__';

    /** @var string */
    private const DESCRIPTION = '__dummy_description__';

    /** @var array<string, mixed> */
    private const PARAMS = [
        'enabled' => true,
        'name' => self::NAME,
        'description' => self::DESCRIPTION,
    ];

    /** @var string */
    private string $roleId;

    /**
     * @return void
     */
    public function testRoleNotFound(): void
    {
        $expectedBody = [
            'status' => [
                'success' => false,
                'errors' => [['code' => 's2.e303.c23', 'message' => 'role not found']],
            ],
        ];

        $this->roleId = "1";
        $this->client->request('POST', '/roles/1', self::PARAMS, [], self::HEADERS);
        $response = $this->client->getResponse();
        $responseBody = json_decode((string) $response->getContent(), true);

        $this->assertEquals(Response::HTTP_NOT_FOUND, $response->getStatusCode());
        $this->assertEquals($expectedBody, $responseBody);

        $this->assertLogFailure(
            'roles.update',
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
                'errors' => [['code' => 's2.e303.c23', 'message' => 'role not found']],
            ],
        ];

        $this->roleId = "1";
        $this->client->request('POST', '/roles/1', self::PARAMS, [], self::HEADERS);
        $response = $this->client->getResponse();
        $responseBody = json_decode((string) $response->getContent(), true);

        $this->assertEquals(Response::HTTP_NOT_FOUND, $response->getStatusCode());
        $this->assertEquals($expectedBody, $responseBody);

        $this->assertLogFailure(
            'roles.update',
            Response::HTTP_NOT_FOUND,
            $expectedBody,
            [[$this, 'assertLogRequestValues']]
        );
    }

    /**
     * @return void
     * @throws Exception
     */
    public function testDuplicateName(): void
    {
        $fixtures = new LocalFixtures();
        $fixtures->addRole();
        $role = $fixtures->addRole(['name' => '__dummy_other_name__']);
        $this->loadFixtures($fixtures);

        $expectedBody = [
            'status' => [
                'success' => false,
                'errors' => [['code' => 's2.e303.c24', 'message' => 'role name already existent in the database']],
            ],
        ];

        $this->roleId = (string) $role->getId();
        $this->client->request('POST', '/roles/' . $this->roleId, self::PARAMS, [], self::HEADERS);
        $response = $this->client->getResponse();
        $responseBody = json_decode((string) $response->getContent(), true);

        $this->assertEquals(Response::HTTP_CONFLICT, $response->getStatusCode());
        $this->assertEquals($expectedBody, $responseBody);

        $this->assertLogFailure(
            'roles.update',
            Response::HTTP_CONFLICT,
            $expectedBody,
            [[$this, 'assertLogRequestValues']]
        );
    }

    /**
     * @return void
     * @throws Exception
     */
    public function testUpdateRole(): void
    {
        $fixtures = new LocalFixtures();
        $fixtures->addUser(['email' => 'root@email.com']);
        $role = $fixtures->addRole(['name' => '__dummy_other_nane__', 'description' => '__dummy_other_description__']);
        $this->loadFixtures($fixtures);

        $expectedBody = [
            'status' => [
                'success' => true,
                'errors' => [],
            ],
            'data' => [
                'role' => [
                    'id' => $role->getId(),
                    'enabled' => true,
                    'name' => self::NAME,
                    'description' => self::DESCRIPTION,
                    'updatedBy' => 'root@email.com',
                ],
            ],
        ];

        $this->roleId = (string) $role->getId();
        $this->client->request('POST', '/roles/' . $this->roleId, self::PARAMS, [], self::HEADERS);
        $response = $this->client->getResponse();
        $responseBody = json_decode((string) $response->getContent(), true);

        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
        $expectedBody['data']['role']['createdAt'] = $responseBody['data']['role']['createdAt'];
        $expectedBody['data']['role']['updatedAt'] = $responseBody['data']['role']['updatedAt'];
        $this->assertEquals($expectedBody, $responseBody);

        $roles = $this->localManager->getRepository(Role::class)->findAll();
        $this->assertCount(1, $roles);
        $this->assertTrue($roles[0]->isEnabled());
        $this->assertEquals($role->getId(), $roles[0]->getId());
        $this->assertEquals(self::NAME, $roles[0]->getName());
        $this->assertEquals(self::DESCRIPTION, $roles[0]->getDescription());

        $this->assertLogSuccess(
            'roles.update',
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
            $this->assertLogRequestAttr($record, "roleId", $this->roleId);
            $this->assertLogRequestBody($record, self::PARAMS);
        }
    }
}
