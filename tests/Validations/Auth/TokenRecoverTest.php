<?php

namespace App\Tests\Validations\Auth;

use App\Tests\EndpointTester;
use Symfony\Component\HttpFoundation\Response;

class TokenRecoverTest extends EndpointTester
{
    /**
     * @param array<string, string> $params
     * @param string $code
     * @param string $message
     * @return void
     * @dataProvider providerForInvalidTests
     */
    public function testInvalid(array $params, string $code, string $message): void
    {
        $expectedStatusCode = Response::HTTP_BAD_REQUEST;

        $this->client->request('PUT', '/auth', $params);
        $response = $this->client->getResponse();
        $responseBody = json_decode((string) $response->getContent(), true);

        $this->assertEquals($expectedStatusCode, $response->getStatusCode());
        $this->assertFalse($responseBody['status']['success']);
        $this->assertTrue(count($responseBody['status']['errors']) > 0);

        $found = false;
        foreach ($responseBody['status']['errors'] as $error) {
            $found = $found || ($error['code'] == $code && $error['message'] == $message);
        }

        $this->assertTrue($found, sprintf('Expected error code/message (%s/%s) not found', $code, $message));
    }

    /**
     * @return array<string, array<string, mixed>>
     */
    public function providerForInvalidTests(): array
    {
        return [
            'missing jwt value' => [
                'params' => [],
                'code' => 's2.e101.p1.c370',
                'message' => 'jwt parameter must be present'
            ],
            'empty jwt value' => [
                'params' => ['jwt' => ''],
                'code' => 's2.e101.p1.c370',
                'message' => 'jwt parameter must be present'
            ],
            'invalid jwt value' => [
                'params' => ['jwt' => [1]],
                'code' => 's2.e101.p1.c460',
                'message' => 'jwt parameter must be of type string'
            ],
            'missing recover value' => [
                'params' => [],
                'code' => 's2.e101.p2.c370',
                'message' => 'recover parameter must be present'
            ],
            'empty recover value' => [
                'params' => ['recover' => ''],
                'code' => 's2.e101.p2.c370',
                'message' => 'recover parameter must be present'
            ],
            'invalid recover value' => [
                'params' => ['recover' => [1]],
                'code' => 's2.e101.p2.c460',
                'message' => 'recover parameter must be of type string'
            ],
        ];
    }
}
