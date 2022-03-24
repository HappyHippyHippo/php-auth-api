<?php

namespace App\Tests\Flow\Users;

use App\Model\Entity\Local\User;
use App\Tests\Flow\EndpointTester;
use App\Tests\LocalFixtures;
use DateTime;
use Exception;
use Symfony\Component\HttpFoundation\Response;

class WarmupTest extends EndpointTester
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
                'errors' => [['code' => 's2.e206.c10', 'message' => 'user not found']],
            ],
        ];

        $this->userId = "1";
        $this->client->request('POST', '/users/1/warmup', [], [], self::HEADERS);
        $response = $this->client->getResponse();
        $responseBody = json_decode((string) $response->getContent(), true);

        $this->assertEquals(Response::HTTP_NOT_FOUND, $response->getStatusCode());
        $this->assertEquals($expectedBody, $responseBody);

        $this->assertLogFailure(
            'users.warmup',
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
                'errors' => [['code' => 's2.e206.c10', 'message' => 'user not found']],
            ],
        ];

        $this->userId = "1";
        $this->client->request('POST', '/users/1/warmup', [], [], self::HEADERS);
        $response = $this->client->getResponse();
        $responseBody = json_decode((string) $response->getContent(), true);

        $this->assertEquals(Response::HTTP_NOT_FOUND, $response->getStatusCode());
        $this->assertEquals($expectedBody, $responseBody);

        $this->assertLogFailure(
            'users.warmup',
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
        $user = $fixtures->addUser(['tries' => 0, 'coolDown' => new DateTime()]);
        $this->loadFixtures($fixtures);

        $expectedBody = [
            'status' => ['success' => true, 'errors' => []],
            'data' => [
                'user' => array_merge(
                    $user->jsonSerialize(),
                    [
                        'tries' => 3,
                        'coolDown' => '1900-01-01 00:00:00',
                        'updatedBy' => $root->getEmail()
                    ]
                )
            ],
        ];

        $this->userId = (string) $user->getId();
        $this->client->request('POST', '/users/' . $this->userId . '/warmup', [], [], self::HEADERS);
        $response = $this->client->getResponse();
        $responseBody = json_decode((string) $response->getContent(), true);

        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
        $expectedBody['data']['user']['updatedAt'] = $responseBody['data']['user']['updatedAt'];
        $this->assertEquals($expectedBody, $responseBody);

        $users = $this->localManager->getRepository(User::class)->findAll();
        $this->assertCount(2, $users);
        $this->assertEquals(3, $users[1]->getTries());
        $this->assertEquals(new DateTime('1900-01-01 00:00:00'), $users[1]->getCoolDown());

        $this->assertLogSuccess(
            'users.warmup',
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
