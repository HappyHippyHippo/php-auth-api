<?php

namespace App\Tests\Validations\Roles;

use App\Tests\EndpointTester;
use Symfony\Component\HttpFoundation\Response;

class UpdateTest extends EndpointTester
{
    /**
     * @param string $roleId
     * @param array<string, string> $params
     * @param array<string, string|string[]> $headers
     * @param string $code
     * @param string $message
     * @return void
     * @dataProvider providerForInvalidTests
     */
    public function testInvalid(string $roleId, array $params, array $headers, string $code, string $message): void
    {
        $this->client->request('POST', '/roles/' . $roleId, $params, [], $headers);
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
                'roleId' => '1',
                'params' => ['id' => 1],
                'headers' => [],
                'code' => 's2.e303.p1.c370',
                'message' => 'x-request-id header must be present',
            ],
            'missing auth token id header' => [
                'roleId' => '1',
                'params' => ['id' => 1],
                'headers' => [],
                'code' => 's2.e303.p2.c370',
                'message' => 'x-auth-token-id header must be present',
            ],
            'invalid string auth token id header' => [
                'roleId' => '1',
                'params' => ['id' => 1],
                'headers' => ['HTTP_x-auth-token-id' => '__dummy_invalid__'],
                'code' => 's2.e303.p2.c430',
                'message' => 'x-auth-token-id header must be an integer',
            ],
            'invalid float auth token id header' => [
                'roleId' => '1',
                'params' => ['id' => 1],
                'headers' => ['HTTP_x-auth-token-id' => 1.1],
                'code' => 's2.e303.p2.c430',
                'message' => 'x-auth-token-id header must be an integer',
            ],
            'invalid negative auth token id header' => [
                'roleId' => '1',
                'params' => ['id' => 1],
                'headers' => ['HTTP_x-auth-token-id' => -1],
                'code' => 's2.e303.p2.c430',
                'message' => 'x-auth-token-id header must be an integer',
            ],
            'invalid zero auth token id header' => [
                'roleId' => '1',
                'params' => ['id' => 1],
                'headers' => ['HTTP_x-auth-token-id' => 0],
                'code' => 's2.e303.p2.c170',
                'message' => 'x-auth-token-id header must be a positive integer',
            ],
            'missing auth user id header' => [
                'roleId' => '1',
                'params' => ['id' => 1],
                'headers' => [],
                'code' => 's2.e303.p3.c370',
                'message' => 'x-auth-user-id header must be present',
            ],
            'invalid string auth user id header' => [
                'roleId' => '1',
                'params' => ['id' => 1],
                'headers' => ['HTTP_x-auth-user-id' => '__dummy_invalid__'],
                'code' => 's2.e303.p3.c430',
                'message' => 'x-auth-user-id header must be an integer',
            ],
            'invalid float auth user id header' => [
                'roleId' => '1',
                'params' => ['id' => 1],
                'headers' => ['HTTP_x-auth-user-id' => 1.1],
                'code' => 's2.e303.p3.c430',
                'message' => 'x-auth-user-id header must be an integer',
            ],
            'invalid negative auth user id header' => [
                'roleId' => '1',
                'params' => ['id' => 1],
                'headers' => ['HTTP_x-auth-user-id' => -1],
                'code' => 's2.e303.p3.c430',
                'message' => 'x-auth-user-id header must be an integer',
            ],
            'invalid zero auth user id header' => [
                'roleId' => '1',
                'params' => ['id' => 1],
                'headers' => ['HTTP_x-auth-user-id' => 0],
                'code' => 's2.e303.p3.c170',
                'message' => 'x-auth-user-id header must be a positive integer',
            ],
            'missing auth user email header' => [
                'roleId' => '1',
                'params' => ['id' => 1],
                'headers' => [],
                'code' => 's2.e303.p4.c370',
                'message' => 'x-auth-user-email header must be present',
            ],
            'invalid auth user email header' => [
                'roleId' => '1',
                'params' => ['id' => 1],
                'headers' => ['HTTP_x-auth-user-email' => '__dummy_invalid__'],
                'code' => 's2.e303.p4.c120',
                'message' => 'x-auth-user-email header must be a valid email',
            ],
            'invalid string role id' => [
                'roleId' => '__dummy_string__',
                'params' => ['id' => 1],
                'headers' => [],
                'code' => 's2.e303.p10.c460',
                'message' => 'roleId parameter must be of type int',
            ],
            'invalid float role id' => [
                'roleId' => '1.1',
                'params' => ['id' => 1],
                'headers' => [],
                'code' => 's2.e303.p10.c460',
                'message' => 'roleId parameter must be of type int',
            ],
            'invalid negative role id' => [
                'roleId' => '-1',
                'params' => ['id' => 1],
                'headers' => [],
                'code' => 's2.e303.p10.c170',
                'message' => 'roleId parameter must be a positive integer',
            ],
            'invalid zero role id' => [
                'roleId' => '0',
                'params' => ['id' => 1],
                'headers' => [],
                'code' => 's2.e303.p10.c170',
                'message' => 'roleId parameter must be a positive integer',
            ],
            'missing enabled' => [
                'roleId' => '1',
                'params' => [],
                'headers' => [],
                'code' => 's2.e303.p11.c370',
                'message' => 'enabled parameter must be present',
            ],
            'empty enabled' => [
                'roleId' => '1',
                'params' => ['enabled' => ''],
                'headers' => [],
                'code' => 's2.e303.p11.c370',
                'message' => 'enabled parameter must be present',
            ],
            'invalid enabled' => [
                'roleId' => '1',
                'params' => ['enabled' => '__invalid__'],
                'headers' => [],
                'code' => 's2.e303.p11.c460',
                'message' => 'enabled parameter must be of type bool',
            ],
            'missing name' => [
                'roleId' => '1',
                'params' => [],
                'headers' => [],
                'code' => 's2.e303.p12.c370',
                'message' => 'name parameter must be present',
            ],
            'empty name' => [
                'roleId' => '1',
                'params' => ['name' => ''],
                'headers' => [],
                'code' => 's2.e303.p12.c370',
                'message' => 'name parameter must be present',
            ],
            'invalid name value' => [
                'roleId' => '1',
                'params' => ['name' => [1]],
                'headers' => [],
                'code' => 's2.e303.p12.c460',
                'message' => 'name parameter must be of type string'
            ],
            'invalid description value' => [
                'roleId' => '1',
                'params' => ['description' => [1]],
                'headers' => [],
                'code' => 's2.e303.p13.c460',
                'message' => 'description parameter must be of type string'
            ],
        ];
    }
}
