<?php

namespace App\Tests\Flow\Auth;

use App\Tests\Flow\EndpointTester;
use App\Tests\LocalFixtures;
use DateTime;
use Exception;
use Symfony\Component\HttpFoundation\Response;

class TokenCheckTest extends EndpointTester
{
    /** @var string */
    private const JWT = '__dummy_jwt__';

    /**
     * @return void
     */
    public function testTokenNotFound(): void
    {
        $expectedBody = [
            'status' => [
                'success' => false,
                'errors' => [['code' => 's2.e100.c20', 'message' => 'token not found']],
            ],
        ];

        $this->client->request('GET', '/auth', ['jwt' => self::JWT]);
        $response = $this->client->getResponse();
        $responseBody = json_decode((string) $response->getContent(), true);

        $this->assertEquals(Response::HTTP_NOT_FOUND, $response->getStatusCode());
        $this->assertEquals($expectedBody, $responseBody);

        $this->assertLogFailure(
            'auth.token.check',
            Response::HTTP_NOT_FOUND,
            $expectedBody,
            [[$this, 'assertLogRequestValues']]
        );
    }

    /**
     * @return void
     * @throws Exception
     */
    public function testTokenDeleted(): void
    {
        $fixtures = new LocalFixtures();
        $user = $fixtures->addUser();
        $fixtures->addToken(['user' => $user, 'jwt' => self::JWT, 'deletedAt' => new DateTime()]);
        $this->loadFixtures($fixtures);

        $expectedBody = [
            'status' => [
                'success' => false,
                'errors' => [['code' => 's2.e100.c20', 'message' => 'token not found']],
            ],
        ];

        $this->client->request('GET', '/auth', ['jwt' => self::JWT]);
        $response = $this->client->getResponse();
        $responseBody = json_decode((string) $response->getContent(), true);

        $this->assertEquals(Response::HTTP_NOT_FOUND, $response->getStatusCode());
        $this->assertEquals($expectedBody, $responseBody);

        $this->assertLogFailure(
            'auth.token.check',
            Response::HTTP_NOT_FOUND,
            $expectedBody,
            [[$this, 'assertLogRequestValues']]
        );
    }

    /**
     * @return void
     * @throws Exception
     */
    public function testTokenExpired(): void
    {
        $fixtures = new LocalFixtures();
        $user = $fixtures->addUser();
        $fixtures->addToken(['user' => $user, 'jwt' => self::JWT, 'ttl' => new DateTime('-2 minutes')]);
        $this->loadFixtures($fixtures);

        $expectedBody = [
            'status' => [
                'success' => false,
                'errors' => [['code' => 's2.e100.c21', 'message' => 'token expired']],
            ],
        ];

        $this->client->request('GET', '/auth', ['jwt' => self::JWT]);
        $response = $this->client->getResponse();
        $responseBody = json_decode((string) $response->getContent(), true);

        $this->assertEquals(Response::HTTP_GONE, $response->getStatusCode());
        $this->assertEquals($expectedBody, $responseBody);

        $this->assertLogFailure(
            'auth.token.check',
            Response::HTTP_GONE,
            $expectedBody,
            [[$this, 'assertLogRequestValues']]
        );
    }

    /**
     * @return void
     * @throws Exception
     */
    public function testValidateToken(): void
    {
        $fixtures = new LocalFixtures();
        $user = $fixtures->addUser();
        $fixtures->addToken(['user' => $user, 'jwt' => self::JWT]);
        $this->loadFixtures($fixtures);

        $this->client->request('GET', '/auth', ['jwt' => self::JWT]);
        $response = $this->client->getResponse();

        $this->assertEquals(Response::HTTP_NO_CONTENT, $response->getStatusCode());

        $this->assertLogSuccess(
            'auth.token.check',
            Response::HTTP_NO_CONTENT,
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
            $this->assertLogRequestQuery($record, 'jwt', self::JWT);
        }
    }
}
