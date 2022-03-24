<?php

namespace App\Tests\Validations\Users;

use App\Tests\EndpointTester;
use Symfony\Component\HttpFoundation\Response;

class EnableTest extends EndpointTester
{
    /**
     * @param array<string, string> $params
     * @param array<string, string|string[]> $headers
     * @param string $code
     * @param string $message
     * @return void
     * @dataProvider providerForInvalidTests
     */
    public function testInvalid(array $params, array $headers, string $code, string $message): void
    {
        $this->client->request('POST', '/users/' . $params['id'] . '/enable', [], [], $headers);
        $response = $this->client->getResponse();
        $responseBody = json_decode((string) $response->getContent(), true);

        $this->assertEquals(Response::HTTP_BAD_REQUEST, $response->getStatusCode());
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
            'missing request id header' => [
                'params' => ['id' => 1],
                'headers' => [],
                'code' => 's2.e204.p1.c370',
                'message' => 'x-request-id header must be present',
            ],
            'missing auth token id header' => [
                'params' => ['id' => 1],
                'headers' => [],
                'code' => 's2.e204.p2.c370',
                'message' => 'x-auth-token-id header must be present',
            ],
            'invalid string auth token id header' => [
                'params' => ['id' => 1],
                'headers' => ['HTTP_x-auth-token-id' => '__dummy_invalid__'],
                'code' => 's2.e204.p2.c430',
                'message' => 'x-auth-token-id header must be an integer',
            ],
            'invalid float auth token id header' => [
                'params' => ['id' => 1],
                'headers' => ['HTTP_x-auth-token-id' => 1.1],
                'code' => 's2.e204.p2.c430',
                'message' => 'x-auth-token-id header must be an integer',
            ],
            'invalid negative auth token id header' => [
                'params' => ['id' => 1],
                'headers' => ['HTTP_x-auth-token-id' => -1],
                'code' => 's2.e204.p2.c430',
                'message' => 'x-auth-token-id header must be an integer',
            ],
            'invalid zero auth token id header' => [
                'params' => ['id' => 1],
                'headers' => ['HTTP_x-auth-token-id' => 0],
                'code' => 's2.e204.p2.c170',
                'message' => 'x-auth-token-id header must be a positive integer',
            ],
            'missing auth user id header' => [
                'params' => ['id' => 1],
                'headers' => [],
                'code' => 's2.e204.p3.c370',
                'message' => 'x-auth-user-id header must be present',
            ],
            'invalid string auth user id header' => [
                'params' => ['id' => 1],
                'headers' => ['HTTP_x-auth-user-id' => '__dummy_invalid__'],
                'code' => 's2.e204.p3.c430',
                'message' => 'x-auth-user-id header must be an integer',
            ],
            'invalid float auth user id header' => [
                'params' => ['id' => 1],
                'headers' => ['HTTP_x-auth-user-id' => 1.1],
                'code' => 's2.e204.p3.c430',
                'message' => 'x-auth-user-id header must be an integer',
            ],
            'invalid negative auth user id header' => [
                'params' => ['id' => 1],
                'headers' => ['HTTP_x-auth-user-id' => -1],
                'code' => 's2.e204.p3.c430',
                'message' => 'x-auth-user-id header must be an integer',
            ],
            'invalid zero auth user id header' => [
                'params' => ['id' => 1],
                'headers' => ['HTTP_x-auth-user-id' => 0],
                'code' => 's2.e204.p3.c170',
                'message' => 'x-auth-user-id header must be a positive integer',
            ],
            'missing auth user email header' => [
                'params' => ['id' => 1],
                'headers' => [],
                'code' => 's2.e204.p4.c370',
                'message' => 'x-auth-user-email header must be present',
            ],
            'invalid auth user email header' => [
                'params' => ['id' => 1],
                'headers' => ['HTTP_x-auth-user-email' => '__dummy_invalid__'],
                'code' => 's2.e204.p4.c120',
                'message' => 'x-auth-user-email header must be a valid email',
            ],
            'invalid string id' => [
                'params' => ['id' => '__invalid_string__'],
                'headers' => [],
                'code' => 's2.e204.p10.c460',
                'message' => 'userId parameter must be of type int',
            ],
            'invalid float id' => [
                'params' => ['id' => 1.1],
                'headers' => [],
                'code' => 's2.e204.p10.c460',
                'message' => 'userId parameter must be of type int',
            ],
            'invalid negative id' => [
                'params' => ['id' => -1],
                'headers' => [],
                'code' => 's2.e204.p10.c170',
                'message' => 'userId parameter must be a positive integer',
            ],
            'invalid zero id' => [
                'params' => ['id' => 0],
                'headers' => [],
                'code' => 's2.e204.p10.c170',
                'message' => 'userId parameter must be a positive integer',
            ],
        ];
    }
}
