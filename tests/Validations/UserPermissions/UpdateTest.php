<?php

namespace App\Tests\Validations\UserPermissions;

use App\Tests\EndpointTester;
use Symfony\Component\HttpFoundation\Response;

class UpdateTest extends EndpointTester
{
    /**
     * @param string $userId
     * @param string $permissionId
     * @param array<string, string> $params
     * @param array<string, string|string[]> $headers
     * @param string $code
     * @param string $message
     * @return void
     * @dataProvider providerForInvalidTests
     */
    public function testInvalid(
        string $userId,
        string $permissionId,
        array $params,
        array $headers,
        string $code,
        string $message
    ): void {
        $this->client->request('POST', '/users/' . $userId . '/permissions/' . $permissionId, $params, [], $headers);
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
                'permissionId' => '1',
                'params' => [],
                'headers' => [],
                'code' => 's2.e503.p1.c370',
                'message' => 'x-request-id header must be present',
            ],
            'missing auth token id header' => [
                'userId' => '1',
                'permissionId' => '1',
                'params' => [],
                'headers' => [],
                'code' => 's2.e503.p2.c370',
                'message' => 'x-auth-token-id header must be present',
            ],
            'invalid string auth token id header' => [
                'userId' => '1',
                'permissionId' => '1',
                'params' => [],
                'headers' => ['HTTP_x-auth-token-id' => '__dummy_invalid__'],
                'code' => 's2.e503.p2.c430',
                'message' => 'x-auth-token-id header must be an integer',
            ],
            'invalid float auth token id header' => [
                'userId' => '1',
                'permissionId' => '1',
                'params' => [],
                'headers' => ['HTTP_x-auth-token-id' => 1.1],
                'code' => 's2.e503.p2.c430',
                'message' => 'x-auth-token-id header must be an integer',
            ],
            'invalid negative auth token id header' => [
                'userId' => '1',
                'permissionId' => '1',
                'params' => [],
                'headers' => ['HTTP_x-auth-token-id' => -1],
                'code' => 's2.e503.p2.c430',
                'message' => 'x-auth-token-id header must be an integer',
            ],
            'invalid zero auth token id header' => [
                'userId' => '1',
                'permissionId' => '1',
                'params' => [],
                'headers' => ['HTTP_x-auth-token-id' => 0],
                'code' => 's2.e503.p2.c170',
                'message' => 'x-auth-token-id header must be a positive integer',
            ],
            'missing auth user id header' => [
                'userId' => '1',
                'permissionId' => '1',
                'params' => [],
                'headers' => [],
                'code' => 's2.e503.p3.c370',
                'message' => 'x-auth-user-id header must be present',
            ],
            'invalid string auth user id header' => [
                'userId' => '1',
                'permissionId' => '1',
                'params' => [],
                'headers' => ['HTTP_x-auth-user-id' => '__dummy_invalid__'],
                'code' => 's2.e503.p3.c430',
                'message' => 'x-auth-user-id header must be an integer',
            ],
            'invalid float auth user id header' => [
                'userId' => '1',
                'permissionId' => '1',
                'params' => [],
                'headers' => ['HTTP_x-auth-user-id' => 1.1],
                'code' => 's2.e503.p3.c430',
                'message' => 'x-auth-user-id header must be an integer',
            ],
            'invalid negative auth user id header' => [
                'userId' => '1',
                'permissionId' => '1',
                'params' => [],
                'headers' => ['HTTP_x-auth-user-id' => -1],
                'code' => 's2.e503.p3.c430',
                'message' => 'x-auth-user-id header must be an integer',
            ],
            'invalid zero auth user id header' => [
                'userId' => '1',
                'permissionId' => '1',
                'params' => [],
                'headers' => ['HTTP_x-auth-user-id' => 0],
                'code' => 's2.e503.p3.c170',
                'message' => 'x-auth-user-id header must be a positive integer',
            ],
            'missing auth user email header' => [
                'userId' => '1',
                'permissionId' => '1',
                'params' => [],
                'headers' => [],
                'code' => 's2.e503.p4.c370',
                'message' => 'x-auth-user-email header must be present',
            ],
            'invalid auth user email header' => [
                'userId' => '1',
                'permissionId' => '1',
                'params' => [],
                'headers' => ['HTTP_x-auth-user-email' => '__dummy_invalid__'],
                'code' => 's2.e503.p4.c120',
                'message' => 'x-auth-user-email header must be a valid email',
            ],
            'invalid string user id' => [
                'userId' => '__invalid_string__',
                'permissionId' => '1',
                'params' => [],
                'headers' => [],
                'code' => 's2.e503.p10.c460',
                'message' => 'userId parameter must be of type int',
            ],
            'invalid float user id' => [
                'userId' => '1.1',
                'permissionId' => '1',
                'params' => [],
                'headers' => [],
                'code' => 's2.e503.p10.c460',
                'message' => 'userId parameter must be of type int',
            ],
            'invalid negative user id' => [
                'userId' => '-1',
                'permissionId' => '1',
                'params' => [],
                'headers' => [],
                'code' => 's2.e503.p10.c170',
                'message' => 'userId parameter must be a positive integer',
            ],
            'invalid zero user id' => [
                'userId' => '0',
                'permissionId' => '1',
                'params' => [],
                'headers' => [],
                'code' => 's2.e503.p10.c170',
                'message' => 'userId parameter must be a positive integer',
            ],
            'invalid string permission id' => [
                'userId' => '1',
                'permissionId' => '__invalid_string__',
                'params' => [],
                'headers' => [],
                'code' => 's2.e503.p11.c460',
                'message' => 'permissionId parameter must be of type int',
            ],
            'invalid float permission id' => [
                'userId' => '1',
                'permissionId' => '1.1',
                'params' => [],
                'headers' => [],
                'code' => 's2.e503.p11.c460',
                'message' => 'permissionId parameter must be of type int',
            ],
            'invalid negative permission id' => [
                'userId' => '1',
                'permissionId' => '-1',
                'params' => [],
                'headers' => [],
                'code' => 's2.e503.p11.c170',
                'message' => 'permissionId parameter must be a positive integer',
            ],
            'invalid zero permission id' => [
                'userId' => '1',
                'permissionId' => '0',
                'params' => [],
                'headers' => [],
                'code' => 's2.e503.p11.c170',
                'message' => 'permissionId parameter must be a positive integer',
            ],
            'missing enabled' => [
                'userId' => '1',
                'permissionId' => '1',
                'params' => [],
                'headers' => [],
                'code' => 's2.e503.p12.c370',
                'message' => 'enabled parameter must be present',
            ],
            'empty enabled' => [
                'userId' => '1',
                'permissionId' => '1',
                'params' => ['enabled' => ''],
                'headers' => [],
                'code' => 's2.e503.p12.c370',
                'message' => 'enabled parameter must be present',
            ],
            'invalid enabled' => [
                'userId' => '1',
                'permissionId' => '1',
                'params' => ['enabled' => '__invalid__'],
                'headers' => [],
                'code' => 's2.e503.p12.c460',
                'message' => 'enabled parameter must be of type bool',
            ],
            'missing directory' => [
                'userId' => '1',
                'permissionId' => '1',
                'params' => [],
                'headers' => [],
                'code' => 's2.e503.p13.c370',
                'message' => 'directory parameter must be present',
            ],
            'empty directory' => [
                'userId' => '1',
                'permissionId' => '1',
                'params' => ['directory' => ''],
                'headers' => [],
                'code' => 's2.e503.p13.c370',
                'message' => 'directory parameter must be present',
            ],
            'invalid directory value' => [
                'userId' => '1',
                'permissionId' => '1',
                'params' => ['directory' => [1]],
                'headers' => [],
                'code' => 's2.e503.p13.c460',
                'message' => 'directory parameter must be of type string'
            ],
            'missing level' => [
                'userId' => '1',
                'permissionId' => '1',
                'params' => [],
                'headers' => [],
                'code' => 's2.e503.p14.c370',
                'message' => 'level parameter must be present',
            ],
            'empty level' => [
                'userId' => '1',
                'permissionId' => '1',
                'params' => ['level' => ''],
                'headers' => [],
                'code' => 's2.e503.p14.c370',
                'message' => 'level parameter must be present',
            ],
            'invalid level choice' => [
                'userId' => '1',
                'permissionId' => '1',
                'params' => ['level' => '__dummy_invalid__'],
                'headers' => [],
                'code' => 's2.e503.p14.c50',
                'message' => 'level parameter must be on of [none, self, group, all]'
            ],
            'invalid level value' => [
                'userId' => '1',
                'permissionId' => '1',
                'params' => ['level' => [1]],
                'headers' => [],
                'code' => 's2.e503.p14.c460',
                'message' => 'level parameter must be of type string'
            ],
            'invalid description value' => [
                'userId' => '1',
                'permissionId' => '1',
                'params' => ['description' => [1]],
                'headers' => [],
                'code' => 's2.e503.p15.c460',
                'message' => 'description parameter must be of type string'
            ],
        ];
    }
}
