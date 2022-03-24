<?php

namespace App\Tests\Flow\Roles;

use App\Model\Entity\Local\Role;
use App\Tests\Flow\EndpointTester;
use App\Tests\LocalFixtures;
use Exception;
use Symfony\Component\HttpFoundation\Response;

class CreateTest extends EndpointTester
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

    /**
     * @return void
     * @throws Exception
     */
    public function testDuplicateName(): void
    {
        $fixtures = new LocalFixtures();
        $fixtures->addRole();
        $this->loadFixtures($fixtures);

        $expectedBody = [
            'status' => [
                'success' => false,
                'errors' => [['code' => 's2.e302.c24', 'message' => 'role name already existent in the database']],
            ],
        ];

        $this->client->request('POST', '/roles', self::PARAMS, [], self::HEADERS);
        $response = $this->client->getResponse();
        $responseBody = json_decode((string) $response->getContent(), true);

        $this->assertEquals(Response::HTTP_CONFLICT, $response->getStatusCode());
        $this->assertEquals($expectedBody, $responseBody);

        $roles = $this->localManager->getRepository(Role::class)->findAll();
        $this->assertCount(1, $roles);

        $this->assertLogFailure(
            'roles.create',
            Response::HTTP_CONFLICT,
            $expectedBody,
            [[$this, 'assertLogRequestValues']]
        );
    }

    /**
     * @return void
     * @throws Exception
     */
    public function testCreateRole(): void
    {
        $fixtures = new LocalFixtures();
        $fixtures->addUser(['email' => 'root@email.com']);
        $this->loadFixtures($fixtures);

        $expectedBody = [
            'status' => [
                'success' => true,
                'errors' => [],
            ],
            'data' => [
                'role' => [
                    'id' => 1,
                    'enabled' => true,
                    'name' => self::NAME,
                    'description' => self::DESCRIPTION,
                    'createdAt' => '1900-01-01 00:00:00',
                    'createdBy' => 'root@email.com',
                    'updatedAt' => '1900-01-01 00:00:00',
                    'updatedBy' => 'root@email.com',
                ],
            ],
        ];

        $this->client->request('POST', '/roles', self::PARAMS, [], self::HEADERS);
        $response = $this->client->getResponse();
        $responseBody = json_decode((string) $response->getContent(), true);

        $this->assertEquals(Response::HTTP_CREATED, $response->getStatusCode());
        $expectedBody['data']['role']['createdAt'] = $responseBody['data']['role']['createdAt'];
        $expectedBody['data']['role']['updatedAt'] = $responseBody['data']['role']['updatedAt'];
        $this->assertEquals($expectedBody, $responseBody);

        $roles = $this->localManager->getRepository(Role::class)->findAll();
        $this->assertCount(1, $roles);
        $this->assertTrue($roles[0]->isEnabled());
        $this->assertEquals(1, $roles[0]->getId());
        $this->assertEquals(self::NAME, $roles[0]->getName());

        $this->assertLogSuccess(
            'roles.create',
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
            $this->assertLogRequestBody($record, self::PARAMS);
        }
    }
}
