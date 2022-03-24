<?php

namespace App\Tests\Validations\RolePermissions;

use App\Tests\EndpointTester;
use Symfony\Component\HttpFoundation\Response;

class SearchTest extends EndpointTester
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
        $this->client->request('GET', '/roles/' . $roleId . '/permissions', $params, [], $headers);
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
                'params' => [],
                'headers' => [],
                'code' => 's2.e401.p1.c370',
                'message' => 'x-request-id header must be present',
            ],
            'missing auth token id header' => [
                'roleId' => '1',
                'params' => [],
                'headers' => [],
                'code' => 's2.e401.p2.c370',
                'message' => 'x-auth-token-id header must be present',
            ],
            'invalid string auth token id header' => [
                'roleId' => '1',
                'params' => [],
                'headers' => ['HTTP_x-auth-token-id' => '__dummy_invalid__'],
                'code' => 's2.e401.p2.c430',
                'message' => 'x-auth-token-id header must be an integer',
            ],
            'invalid float auth token id header' => [
                'roleId' => '1',
                'params' => [],
                'headers' => ['HTTP_x-auth-token-id' => 1.1],
                'code' => 's2.e401.p2.c430',
                'message' => 'x-auth-token-id header must be an integer',
            ],
            'invalid negative auth token id header' => [
                'roleId' => '1',
                'params' => [],
                'headers' => ['HTTP_x-auth-token-id' => -1],
                'code' => 's2.e401.p2.c430',
                'message' => 'x-auth-token-id header must be an integer',
            ],
            'invalid zero auth token id header' => [
                'roleId' => '1',
                'params' => [],
                'headers' => ['HTTP_x-auth-token-id' => 0],
                'code' => 's2.e401.p2.c170',
                'message' => 'x-auth-token-id header must be a positive integer',
            ],
            'missing auth user id header' => [
                'roleId' => '1',
                'params' => [],
                'headers' => [],
                'code' => 's2.e401.p3.c370',
                'message' => 'x-auth-user-id header must be present',
            ],
            'invalid string auth user id header' => [
                'roleId' => '1',
                'params' => [],
                'headers' => ['HTTP_x-auth-user-id' => '__dummy_invalid__'],
                'code' => 's2.e401.p3.c430',
                'message' => 'x-auth-user-id header must be an integer',
            ],
            'invalid float auth user id header' => [
                'roleId' => '1',
                'params' => [],
                'headers' => ['HTTP_x-auth-user-id' => 1.1],
                'code' => 's2.e401.p3.c430',
                'message' => 'x-auth-user-id header must be an integer',
            ],
            'invalid negative auth user id header' => [
                'roleId' => '1',
                'params' => [],
                'headers' => ['HTTP_x-auth-user-id' => -1],
                'code' => 's2.e401.p3.c430',
                'message' => 'x-auth-user-id header must be an integer',
            ],
            'invalid zero auth user id header' => [
                'roleId' => '1',
                'params' => [],
                'headers' => ['HTTP_x-auth-user-id' => 0],
                'code' => 's2.e401.p3.c170',
                'message' => 'x-auth-user-id header must be a positive integer',
            ],
            'missing auth user email header' => [
                'roleId' => '1',
                'params' => [],
                'headers' => [],
                'code' => 's2.e401.p4.c370',
                'message' => 'x-auth-user-email header must be present',
            ],
            'invalid auth user email header' => [
                'roleId' => '1',
                'params' => [],
                'headers' => ['HTTP_x-auth-user-email' => '__dummy_invalid__'],
                'code' => 's2.e401.p4.c120',
                'message' => 'x-auth-user-email header must be a valid email',
            ],
            'invalid non string search value' => [
                'roleId' => '1',
                'params' => ['search' => [1]],
                'headers' => [],
                'code' => 's2.e401.p11.c460',
                'message' => 'search parameter must be of type string',
            ],
            'invalid string start' => [
                'roleId' => '1',
                'params' => ['start' => '__invalid_string__'],
                'headers' => [],
                'code' => 's2.e401.p12.c460',
                'message' => 'start parameter must be of type int',
            ],
            'invalid float start' => [
                'roleId' => '1',
                'params' => ['start' => 1.1],
                'headers' => [],
                'code' => 's2.e401.p12.c460',
                'message' => 'start parameter must be of type int',
            ],
            'invalid negative start' => [
                'roleId' => '1',
                'params' => ['start' => -1],
                'headers' => [],
                'code' => 's2.e401.p12.c180',
                'message' => 'start parameter must be a zero or positive integer',
            ],
            'invalid string count' => [
                'roleId' => '1',
                'params' => ['count' => '__invalid_string__'],
                'headers' => [],
                'code' => 's2.e401.p13.c460',
                'message' => 'count parameter must be of type int',
            ],
            'invalid float count' => [
                'roleId' => '1',
                'params' => ['count' => 1.1],
                'headers' => [],
                'code' => 's2.e401.p13.c460',
                'message' => 'count parameter must be of type int',
            ],
            'invalid negative count' => [
                'roleId' => '1',
                'params' => ['count' => -1],
                'headers' => [],
                'code' => 's2.e401.p13.c170',
                'message' => 'count parameter must be a positive integer',
            ],
            'invalid zero count' => [
                'roleId' => '1',
                'params' => ['count' => 0],
                'headers' => [],
                'code' => 's2.e401.p13.c170',
                'message' => 'count parameter must be a positive integer',
            ],
        ];
    }
}
