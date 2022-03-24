<?php

namespace App\Tests\Flow\UserRoles;

use App\Tests\Flow\EndpointTester;
use App\Tests\LocalFixtures;
use DateTime;
use Exception;
use Symfony\Component\HttpFoundation\Response;

class GetTest extends EndpointTester
{
    /** @var string */
    private string $userId;

    /**
     * @return void
     */
    public function testUserNotFound(): void
    {
        $expectedBody = [
            'status' => [
                'success' => false,
                'errors' => [['code' => 's2.e600.c10', 'message' => 'user not found']],
            ],
        ];

        $this->userId = "1";
        $this->client->request('GET', '/users/1/roles', [], [], self::HEADERS);
        $response = $this->client->getResponse();
        $responseBody = json_decode((string) $response->getContent(), true);

        $this->assertEquals(Response::HTTP_NOT_FOUND, $response->getStatusCode());
        $this->assertEquals($expectedBody, $responseBody);

        $this->assertLogFailure(
            'users.roles',
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
                'errors' => [['code' => 's2.e600.c10', 'message' => 'user not found']],
            ],
        ];

        $this->userId = "1";
        $this->client->request('GET', '/users/1/roles', [], [], self::HEADERS);
        $response = $this->client->getResponse();
        $responseBody = json_decode((string) $response->getContent(), true);

        $this->assertEquals(Response::HTTP_NOT_FOUND, $response->getStatusCode());
        $this->assertEquals($expectedBody, $responseBody);

        $this->assertLogFailure(
            'users.roles',
            Response::HTTP_NOT_FOUND,
            $expectedBody,
            [[$this, 'assertLogRequestValues']]
        );
    }

    /**
     * @return void
     * @throws Exception
     */
    public function testGetSuccess(): void
    {
        $fixtures = new LocalFixtures();
        $user = $fixtures->addUser();
        $role1 = $fixtures->addRole(['name' => '__dummy_name_1__']);
        $role2 = $fixtures->addRole(['name' => '__dummy_name_2__']);
        $role3 = $fixtures->addRole(['name' => '__dummy_name_3__']);
        $rel1 = $fixtures->addUserRole(['user' => $user, 'role' => $role1, 'priority' => 1]);
        $rel2 = $fixtures->addUserRole(['user' => $user, 'role' => $role2, 'priority' => 2]);
        $rel3 = $fixtures->addUserRole(['user' => $user, 'role' => $role3, 'priority' => 3]);
        $this->loadFixtures($fixtures);

        $expectedBody = [
            'status' => ['success' => true, 'errors' => []],
            'data' => [
                'roles' => [
                    $role1->getName() => $rel1->jsonSerialize(),
                    $role2->getName() => $rel2->jsonSerialize(),
                    $role3->getName() => $rel3->jsonSerialize(),
                ],
            ],
        ];

        $this->userId = (string) $user->getId();
        $this->client->request('GET', '/users/' . $this->userId . '/roles', [], [], self::HEADERS);
        $response = $this->client->getResponse();
        $responseBody = json_decode((string) $response->getContent(), true);

        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
        $this->assertEquals($expectedBody, $responseBody);

        $this->assertLogSuccess(
            'users.roles',
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
        }
    }
}
