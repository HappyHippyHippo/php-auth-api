<?php

namespace App\Tests\Flow\Users;

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
                'errors' => [['code' => 's2.e200.c10', 'message' => 'user not found']],
            ],
        ];

        $this->userId = "1";
        $this->client->request('GET', '/users/1', [], [], self::HEADERS);
        $response = $this->client->getResponse();
        $responseBody = json_decode((string) $response->getContent(), true);

        $this->assertEquals(Response::HTTP_NOT_FOUND, $response->getStatusCode());
        $this->assertEquals($expectedBody, $responseBody);

        $this->assertLogFailure(
            'users.get',
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
                'errors' => [['code' => 's2.e200.c10', 'message' => 'user not found']],
            ],
        ];

        $this->userId = "1";
        $this->client->request('GET', '/users/1', [], [], self::HEADERS);
        $response = $this->client->getResponse();
        $responseBody = json_decode((string) $response->getContent(), true);

        $this->assertEquals(Response::HTTP_NOT_FOUND, $response->getStatusCode());
        $this->assertEquals($expectedBody, $responseBody);

        $this->assertLogFailure(
            'users.get',
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
        $this->loadFixtures($fixtures);

        $expectedBody = [
            'status' => ['success' => true, 'errors' => []],
            'data' => [
                'user' => $user->jsonSerialize()
            ],
        ];

        $this->userId = (string) $user->getId();
        $this->client->request('GET', '/users/' . $this->userId, [], [], self::HEADERS);
        $response = $this->client->getResponse();
        $responseBody = json_decode((string) $response->getContent(), true);

        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
        $this->assertEquals($expectedBody, $responseBody);

        $this->assertLogSuccess(
            'users.get',
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
