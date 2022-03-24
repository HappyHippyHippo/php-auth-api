<?php

namespace App\Tests\Validations\Auth;

use App\Tests\EndpointTester;
use Symfony\Component\HttpFoundation\Response;

class AuthChapResponseTest extends EndpointTester
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

        $this->client->request('POST', '/auth/chap', $params);
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
            'missing email value' => [
                'params' => [],
                'code' => 's2.e103.p1.c370',
                'message' => 'email parameter must be present'
            ],
            'empty email value' => [
                'params' => ['email' => ''],
                'code' => 's2.e103.p1.c370',
                'message' => 'email parameter must be present'
            ],
            'invalid email value' => [
                'params' => ['email' => [1]],
                'code' => 's2.e103.p1.c460',
                'message' => 'email parameter must be of type string'
            ],
            'invalid email check' => [
                'params' => ['email' => 'invalid.email'],
                'code' => 's2.e103.p1.c120',
                'message' => 'email parameter must be a valid email'
            ],
            'missing challenge value' => [
                'params' => [],
                'code' => 's2.e103.p2.c370',
                'message' => 'challenge parameter must be present'
            ],
            'empty challenge value' => [
                'params' => ['challenge' => ''],
                'code' => 's2.e103.p2.c370',
                'message' => 'challenge parameter must be present'
            ],
            'invalid challenge value' => [
                'params' => ['challenge' => [1]],
                'code' => 's2.e103.p2.c460',
                'message' => 'challenge parameter must be of type string'
            ],
            'missing response value' => [
                'params' => [],
                'code' => 's2.e103.p3.c370',
                'message' => 'response parameter must be present'
            ],
            'empty response value' => [
                'params' => ['response' => ''],
                'code' => 's2.e103.p3.c370',
                'message' => 'response parameter must be present'
            ],
            'invalid response value' => [
                'params' => ['response' => [1]],
                'code' => 's2.e103.p3.c460',
                'message' => 'response parameter must be of type string'
            ],
        ];
    }
}
