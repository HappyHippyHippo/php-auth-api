<?php

namespace App\Tests\Validations\Users;

use App\Tests\EndpointTester;
use Symfony\Component\HttpFoundation\Response;

class UpdateTest extends EndpointTester
{
    /**
     * @param string $userId
     * @param array<string, string> $params
     * @param array<string, string|string[]> $headers
     * @param string $code
     * @param string $message
     * @return void
     * @dataProvider providerForInvalidTests
     */
    public function testInvalid(string $userId, array $params, array $headers, string $code, string $message): void
    {
        $this->client->request('POST', '/users/' . $userId, $params, [], $headers);
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
                'userId' => '1',
                'params' => ['id' => 1],
                'headers' => [],
                'code' => 's2.e203.p1.c370',
                'message' => 'x-request-id header must be present',
            ],
            'missing auth token id header' => [
                'userId' => '1',
                'params' => ['id' => 1],
                'headers' => [],
                'code' => 's2.e203.p2.c370',
                'message' => 'x-auth-token-id header must be present',
            ],
            'invalid string auth token id header' => [
                'userId' => '1',
                'params' => ['id' => 1],
                'headers' => ['HTTP_x-auth-token-id' => '__dummy_invalid__'],
                'code' => 's2.e203.p2.c430',
                'message' => 'x-auth-token-id header must be an integer',
            ],
            'invalid float auth token id header' => [
                'userId' => '1',
                'params' => ['id' => 1],
                'headers' => ['HTTP_x-auth-token-id' => 1.1],
                'code' => 's2.e203.p2.c430',
                'message' => 'x-auth-token-id header must be an integer',
            ],
            'invalid negative auth token id header' => [
                'userId' => '1',
                'params' => ['id' => 1],
                'headers' => ['HTTP_x-auth-token-id' => -1],
                'code' => 's2.e203.p2.c430',
                'message' => 'x-auth-token-id header must be an integer',
            ],
            'invalid zero auth token id header' => [
                'userId' => '1',
                'params' => ['id' => 1],
                'headers' => ['HTTP_x-auth-token-id' => 0],
                'code' => 's2.e203.p2.c170',
                'message' => 'x-auth-token-id header must be a positive integer',
            ],
            'missing auth user id header' => [
                'userId' => '1',
                'params' => ['id' => 1],
                'headers' => [],
                'code' => 's2.e203.p3.c370',
                'message' => 'x-auth-user-id header must be present',
            ],
            'invalid string auth user id header' => [
                'userId' => '1',
                'params' => ['id' => 1],
                'headers' => ['HTTP_x-auth-user-id' => '__dummy_invalid__'],
                'code' => 's2.e203.p3.c430',
                'message' => 'x-auth-user-id header must be an integer',
            ],
            'invalid float auth user id header' => [
                'userId' => '1',
                'params' => ['id' => 1],
                'headers' => ['HTTP_x-auth-user-id' => 1.1],
                'code' => 's2.e203.p3.c430',
                'message' => 'x-auth-user-id header must be an integer',
            ],
            'invalid negative auth user id header' => [
                'userId' => '1',
                'params' => ['id' => 1],
                'headers' => ['HTTP_x-auth-user-id' => -1],
                'code' => 's2.e203.p3.c430',
                'message' => 'x-auth-user-id header must be an integer',
            ],
            'invalid zero auth user id header' => [
                'userId' => '1',
                'params' => ['id' => 1],
                'headers' => ['HTTP_x-auth-user-id' => 0],
                'code' => 's2.e203.p3.c170',
                'message' => 'x-auth-user-id header must be a positive integer',
            ],
            'missing auth user email header' => [
                'userId' => '1',
                'params' => ['id' => 1],
                'headers' => [],
                'code' => 's2.e203.p4.c370',
                'message' => 'x-auth-user-email header must be present',
            ],
            'invalid auth user email header' => [
                'userId' => '1',
                'params' => ['id' => 1],
                'headers' => ['HTTP_x-auth-user-email' => '__dummy_invalid__'],
                'code' => 's2.e203.p4.c120',
                'message' => 'x-auth-user-email header must be a valid email',
            ],
            'invalid string user id' => [
                'userId' => '__dummy_string__',
                'params' => ['id' => 1],
                'headers' => [],
                'code' => 's2.e203.p10.c460',
                'message' => 'userId parameter must be of type int',
            ],
            'invalid float user id' => [
                'userId' => '1.1',
                'params' => ['id' => 1],
                'headers' => [],
                'code' => 's2.e203.p10.c460',
                'message' => 'userId parameter must be of type int',
            ],
            'invalid negative user id' => [
                'userId' => '-1',
                'params' => ['id' => 1],
                'headers' => [],
                'code' => 's2.e203.p10.c170',
                'message' => 'userId parameter must be a positive integer',
            ],
            'invalid zero user id' => [
                'userId' => '0',
                'params' => ['id' => 1],
                'headers' => [],
                'code' => 's2.e203.p10.c170',
                'message' => 'userId parameter must be a positive integer',
            ],
            'missing enabled' => [
                'userId' => '1',
                'params' => [],
                'headers' => [],
                'code' => 's2.e203.p11.c370',
                'message' => 'enabled parameter must be present',
            ],
            'empty enabled' => [
                'userId' => '1',
                'params' => ['enabled' => ''],
                'headers' => [],
                'code' => 's2.e203.p11.c370',
                'message' => 'enabled parameter must be present',
            ],
            'invalid enabled' => [
                'userId' => '1',
                'params' => ['enabled' => '__invalid__'],
                'headers' => [],
                'code' => 's2.e203.p11.c460',
                'message' => 'enabled parameter must be of type bool',
            ],
            'missing email' => [
                'userId' => '1',
                'params' => [],
                'headers' => [],
                'code' => 's2.e203.p12.c370',
                'message' => 'email parameter must be present',
            ],
            'empty email' => [
                'userId' => '1',
                'params' => ['email' => ''],
                'headers' => [],
                'code' => 's2.e203.p12.c370',
                'message' => 'email parameter must be present',
            ],
            'invalid email check' => [
                'userId' => '1',
                'params' => ['email' => '__invalid__'],
                'headers' => [],
                'code' => 's2.e203.p12.c120',
                'message' => 'email parameter must be a valid email',
            ],
            'invalid email value' => [
                'userId' => '1',
                'params' => ['email' => [1]],
                'headers' => [],
                'code' => 's2.e203.p12.c460',
                'message' => 'email parameter must be of type string',
            ],
        ];
    }
}
