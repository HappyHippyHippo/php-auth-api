<?php

namespace App\Tests\Validations\UserRoles;

use App\Tests\EndpointTester;
use Symfony\Component\HttpFoundation\Response;

class PriorityTest extends EndpointTester
{
    /**
     * @param string $userId
     * @param string $roleId
     * @param array<string, mixed[]> $params
     * @param array<string, string|string[]> $headers
     * @param string $code
     * @param string $message
     * @return void
     * @dataProvider providerForInvalidTests
     */
    public function testInvalid(
        string $userId,
        string $roleId,
        array $params,
        array $headers,
        string $code,
        string $message
    ): void {
        $this->client->request(
            'POST',
            '/users/' . $userId . '/roles/' . $roleId . '/priority',
            $params,
            [],
            $headers
        );
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
                'roleId' => '1',
                'params' => [],
                'headers' => [],
                'code' => 's2.e605.p1.c370',
                'message' => 'x-request-id header must be present',
            ],
            'missing auth token id header' => [
                'userId' => '1',
                'roleId' => '1',
                'params' => [],
                'headers' => [],
                'code' => 's2.e605.p2.c370',
                'message' => 'x-auth-token-id header must be present',
            ],
            'invalid string auth token id header' => [
                'userId' => '1',
                'roleId' => '1',
                'params' => [],
                'headers' => ['HTTP_x-auth-token-id' => '__dummy_invalid__'],
                'code' => 's2.e605.p2.c430',
                'message' => 'x-auth-token-id header must be an integer',
            ],
            'invalid float auth token id header' => [
                'userId' => '1',
                'roleId' => '1',
                'params' => [],
                'headers' => ['HTTP_x-auth-token-id' => 1.1],
                'code' => 's2.e605.p2.c430',
                'message' => 'x-auth-token-id header must be an integer',
            ],
            'invalid negative auth token id header' => [
                'userId' => '1',
                'roleId' => '1',
                'params' => [],
                'headers' => ['HTTP_x-auth-token-id' => -1],
                'code' => 's2.e605.p2.c430',
                'message' => 'x-auth-token-id header must be an integer',
            ],
            'invalid zero auth token id header' => [
                'userId' => '1',
                'roleId' => '1',
                'params' => [],
                'headers' => ['HTTP_x-auth-token-id' => 0],
                'code' => 's2.e605.p2.c170',
                'message' => 'x-auth-token-id header must be a positive integer',
            ],
            'missing auth user id header' => [
                'userId' => '1',
                'roleId' => '1',
                'params' => [],
                'headers' => [],
                'code' => 's2.e605.p3.c370',
                'message' => 'x-auth-user-id header must be present',
            ],
            'invalid string auth user id header' => [
                'userId' => '1',
                'roleId' => '1',
                'params' => [],
                'headers' => ['HTTP_x-auth-user-id' => '__dummy_invalid__'],
                'code' => 's2.e605.p3.c430',
                'message' => 'x-auth-user-id header must be an integer',
            ],
            'invalid float auth user id header' => [
                'userId' => '1',
                'roleId' => '1',
                'params' => [],
                'headers' => ['HTTP_x-auth-user-id' => 1.1],
                'code' => 's2.e605.p3.c430',
                'message' => 'x-auth-user-id header must be an integer',
            ],
            'invalid negative auth user id header' => [
                'userId' => '1',
                'roleId' => '1',
                'params' => [],
                'headers' => ['HTTP_x-auth-user-id' => -1],
                'code' => 's2.e605.p3.c430',
                'message' => 'x-auth-user-id header must be an integer',
            ],
            'invalid zero auth user id header' => [
                'userId' => '1',
                'roleId' => '1',
                'params' => [],
                'headers' => ['HTTP_x-auth-user-id' => 0],
                'code' => 's2.e605.p3.c170',
                'message' => 'x-auth-user-id header must be a positive integer',
            ],
            'missing auth user email header' => [
                'userId' => '1',
                'roleId' => '1',
                'params' => [],
                'headers' => [],
                'code' => 's2.e605.p4.c370',
                'message' => 'x-auth-user-email header must be present',
            ],
            'invalid auth user email header' => [
                'userId' => '1',
                'roleId' => '1',
                'params' => [],
                'headers' => ['HTTP_x-auth-user-email' => '__dummy_invalid__'],
                'code' => 's2.e605.p4.c120',
                'message' => 'x-auth-user-email header must be a valid email',
            ],
            'invalid string userId' => [
                'userId' => '__invalid_string__',
                'roleId' => '1',
                'params' => [],
                'headers' => [],
                'code' => 's2.e605.p10.c460',
                'message' => 'userId parameter must be of type int',
            ],
            'invalid float userId' => [
                'userId' => '1.1',
                'roleId' => '1',
                'params' => [],
                'headers' => [],
                'code' => 's2.e605.p10.c460',
                'message' => 'userId parameter must be of type int',
            ],
            'invalid negative userId' => [
                'userId' => '-1',
                'roleId' => '1',
                'params' => [],
                'headers' => [],
                'code' => 's2.e605.p10.c170',
                'message' => 'userId parameter must be a positive integer',
            ],
            'invalid zero userId' => [
                'userId' => '0',
                'roleId' => '1',
                'params' => [],
                'headers' => [],
                'code' => 's2.e605.p10.c170',
                'message' => 'userId parameter must be a positive integer',
            ],
            'invalid string roleId' => [
                'userId' => '1',
                'roleId' => '__invalid_string__',
                'params' => [],
                'headers' => [],
                'code' => 's2.e605.p11.c460',
                'message' => 'roleId parameter must be of type int',
            ],
            'invalid float roleId' => [
                'userId' => '1',
                'roleId' => '1.1',
                'params' => [],
                'headers' => [],
                'code' => 's2.e605.p11.c460',
                'message' => 'roleId parameter must be of type int',
            ],
            'invalid negative roleId' => [
                'userId' => '1',
                'roleId' => '-1',
                'params' => [],
                'headers' => [],
                'code' => 's2.e605.p11.c170',
                'message' => 'roleId parameter must be a positive integer',
            ],
            'invalid zero roleId' => [
                'userId' => '1',
                'roleId' => '0',
                'params' => [],
                'headers' => [],
                'code' => 's2.e605.p11.c170',
                'message' => 'roleId parameter must be a positive integer',
            ],
            'missing priority' => [
                'userId' => '1',
                'roleId' => '__invalid_string__',
                'params' => [],
                'headers' => [],
                'code' => 's2.e605.p12.c370',
                'message' => 'priority parameter must be present',
            ],
            'invalid string priority' => [
                'userId' => '1',
                'roleId' => '1',
                'params' => ['priority' => '__dummy_string__'],
                'headers' => [],
                'code' => 's2.e605.p12.c460',
                'message' => 'priority parameter must be of type int',
            ],
            'invalid float priority' => [
                'userId' => '1',
                'roleId' => '1',
                'params' => ['priority' => '1.1'],
                'headers' => [],
                'code' => 's2.e605.p12.c460',
                'message' => 'priority parameter must be of type int',
            ],
        ];
    }
}
