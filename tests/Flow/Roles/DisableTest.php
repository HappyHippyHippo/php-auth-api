<?php

namespace App\Tests\Flow\Roles;

use App\Model\Entity\Local\Role;
use App\Tests\Flow\EndpointTester;
use App\Tests\LocalFixtures;
use DateTime;
use Exception;
use Symfony\Component\HttpFoundation\Response;

class DisableTest extends EndpointTester
{
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
                'errors' => [['code' => 's2.e305.c23', 'message' => 'role not found']],
            ],
        ];

        $this->roleId = "1";
        $this->client->request('POST', '/roles/1/disable', [], [], self::HEADERS);
        $response = $this->client->getResponse();
        $responseBody = json_decode((string) $response->getContent(), true);

        $this->assertEquals(Response::HTTP_NOT_FOUND, $response->getStatusCode());
        $this->assertEquals($expectedBody, $responseBody);

        $this->assertLogFailure(
            'roles.disable',
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
                'errors' => [['code' => 's2.e305.c23', 'message' => 'role not found']],
            ],
        ];

        $this->roleId = "1";
        $this->client->request('POST', '/roles/1/disable', [], [], self::HEADERS);
        $response = $this->client->getResponse();
        $responseBody = json_decode((string) $response->getContent(), true);

        $this->assertEquals(Response::HTTP_NOT_FOUND, $response->getStatusCode());
        $this->assertEquals($expectedBody, $responseBody);

        $this->assertLogFailure(
            'roles.disable',
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
        $root = $fixtures->addUser(['email' => 'root@email.com']);
        $role = $fixtures->addRole(['enabled' => true]);
        $this->loadFixtures($fixtures);

        $expectedBody = [
            'status' => ['success' => true, 'errors' => []],
            'data' => [
                'role' => array_merge(
                    $role->jsonSerialize(),
                    [
                        'enabled' => false,
                        'updatedBy' => $root->getEmail()
                    ]
                )
            ],
        ];

        $this->roleId = (string) $role->getId();
        $this->client->request('POST', '/roles/' . $this->roleId . '/disable', [], [], self::HEADERS);
        $response = $this->client->getResponse();
        $responseBody = json_decode((string) $response->getContent(), true);

        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
        $expectedBody['data']['role']['updatedAt'] = $responseBody['data']['role']['updatedAt'];
        $this->assertEquals($expectedBody, $responseBody);

        $roles = $this->localManager->getRepository(Role::class)->findAll();
        $this->assertCount(1, $roles);
        $this->assertFalse($roles[0]->isEnabled());

        $this->assertLogSuccess(
            'roles.disable',
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
        }
    }
}
