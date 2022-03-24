<?php

namespace App\Tests\Validations\Users;

use App\Tests\EndpointTester;
use Symfony\Component\HttpFoundation\Response;

class SearchTest extends EndpointTester
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
        $this->client->request('GET', '/users', $params, [], $headers);
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
        $rule = 'This value should satisfy at least one of the following constraints:';
        $rule_1 = ' [1] search parameter is not present';
        $rule_2 = ' [2] search parameter is a string';
        $rule_3_1 = ' [3] search parameter list cannot be empty';
        $rule_3_2 = ' [3] search list parameter element must be an integer';
        $rule_3_3 = ' [3] search list parameter element must be a positive integer';

        return [
            'missing request id header' => [
                'params' => ['id' => 1],
                'headers' => [],
                'code' => 's2.e201.p1.c370',
                'message' => 'x-request-id header must be present',
            ],
            'missing auth token id header' => [
                'params' => ['id' => 1],
                'headers' => [],
                'code' => 's2.e201.p2.c370',
                'message' => 'x-auth-token-id header must be present',
            ],
            'invalid string auth token id header' => [
                'params' => ['id' => 1],
                'headers' => ['HTTP_x-auth-token-id' => '__dummy_invalid__'],
                'code' => 's2.e201.p2.c430',
                'message' => 'x-auth-token-id header must be an integer',
            ],
            'invalid float auth token id header' => [
                'params' => ['id' => 1],
                'headers' => ['HTTP_x-auth-token-id' => 1.1],
                'code' => 's2.e201.p2.c430',
                'message' => 'x-auth-token-id header must be an integer',
            ],
            'invalid negative auth token id header' => [
                'params' => ['id' => 1],
                'headers' => ['HTTP_x-auth-token-id' => -1],
                'code' => 's2.e201.p2.c430',
                'message' => 'x-auth-token-id header must be an integer',
            ],
            'invalid zero auth token id header' => [
                'params' => ['id' => 1],
                'headers' => ['HTTP_x-auth-token-id' => 0],
                'code' => 's2.e201.p2.c170',
                'message' => 'x-auth-token-id header must be a positive integer',
            ],
            'missing auth user id header' => [
                'params' => ['id' => 1],
                'headers' => [],
                'code' => 's2.e201.p3.c370',
                'message' => 'x-auth-user-id header must be present',
            ],
            'invalid string auth user id header' => [
                'params' => ['id' => 1],
                'headers' => ['HTTP_x-auth-user-id' => '__dummy_invalid__'],
                'code' => 's2.e201.p3.c430',
                'message' => 'x-auth-user-id header must be an integer',
            ],
            'invalid float auth user id header' => [
                'params' => ['id' => 1],
                'headers' => ['HTTP_x-auth-user-id' => 1.1],
                'code' => 's2.e201.p3.c430',
                'message' => 'x-auth-user-id header must be an integer',
            ],
            'invalid negative auth user id header' => [
                'params' => ['id' => 1],
                'headers' => ['HTTP_x-auth-user-id' => -1],
                'code' => 's2.e201.p3.c430',
                'message' => 'x-auth-user-id header must be an integer',
            ],
            'invalid zero auth user id header' => [
                'params' => ['id' => 1],
                'headers' => ['HTTP_x-auth-user-id' => 0],
                'code' => 's2.e201.p3.c170',
                'message' => 'x-auth-user-id header must be a positive integer',
            ],
            'missing auth user email header' => [
                'params' => ['id' => 1],
                'headers' => [],
                'code' => 's2.e201.p4.c370',
                'message' => 'x-auth-user-email header must be present',
            ],
            'invalid auth user email header' => [
                'params' => ['id' => 1],
                'headers' => ['HTTP_x-auth-user-email' => '__dummy_invalid__'],
                'code' => 's2.e201.p4.c120',
                'message' => 'x-auth-user-email header must be a valid email',
            ],
            'empty search list' => [
                'params' => ['search' => []],
                'headers' => [],
                'code' => 's2.e201.p10.c1',
                'message' => $rule . $rule_1 . $rule_2 . $rule_3_1,
            ],
            'null search list entry' => [
                'params' => ['search' => [null]],
                'headers' => [],
                'code' => 's2.e201.p10.c1',
                'message' => $rule . $rule_1 . $rule_2 . $rule_3_3,
            ],
            'invalid string search list entry' => [
                'params' => ['search' => ['__invalid_string__']],
                'headers' => [],
                'code' => 's2.e201.p10.c1',
                'message' => $rule . $rule_1 . $rule_2 . $rule_3_2,
            ],
            'invalid float search list entry' => [
                'params' => ['search' => [1.1]],
                'headers' => [],
                'code' => 's2.e201.p10.c1',
                'message' => $rule . $rule_1 . $rule_2 . $rule_3_2,
            ],
            'invalid negative search list entry' => [
                'params' => ['search' => [-1]],
                'headers' => [],
                'code' => 's2.e201.p10.c1',
                'message' => $rule . $rule_1 . $rule_2 . $rule_3_3,
            ],
            'invalid zero search list entry' => [
                'params' => ['search' => [0]],
                'headers' => [],
                'code' => 's2.e201.p10.c1',
                'message' => $rule . $rule_1 . $rule_2 . $rule_3_3,
            ],
            'mixed id and uuid hit on ids check' => [
                'params' => ['search' => [1, '0000a7ca-9ac7-11e6-9629-0ab24b97733e']],
                'headers' => [],
                'code' => 's2.e201.p10.c1',
                'message' => $rule . $rule_1 . $rule_2 . $rule_3_2,
            ],
            'invalid string start' => [
                'params' => ['start' => '__invalid_string__'],
                'headers' => [],
                'code' => 's2.e201.p11.c460',
                'message' => 'start parameter must be of type int',
            ],
            'invalid float start' => [
                'params' => ['start' => 1.1],
                'headers' => [],
                'code' => 's2.e201.p11.c460',
                'message' => 'start parameter must be of type int',
            ],
            'invalid negative start' => [
                'params' => ['start' => -1],
                'headers' => [],
                'code' => 's2.e201.p11.c180',
                'message' => 'start parameter must be a zero or positive integer',
            ],
            'invalid string count' => [
                'params' => ['count' => '__invalid_string__'],
                'headers' => [],
                'code' => 's2.e201.p12.c460',
                'message' => 'count parameter must be of type int',
            ],
            'invalid float count' => [
                'params' => ['count' => 1.1],
                'headers' => [],
                'code' => 's2.e201.p12.c460',
                'message' => 'count parameter must be of type int',
            ],
            'invalid negative count' => [
                'params' => ['count' => -1],
                'headers' => [],
                'code' => 's2.e201.p12.c170',
                'message' => 'count parameter must be a positive integer',
            ],
            'invalid zero count' => [
                'params' => ['count' => 0],
                'headers' => [],
                'code' => 's2.e201.p12.c170',
                'message' => 'count parameter must be a positive integer',
            ],
        ];
    }
}
