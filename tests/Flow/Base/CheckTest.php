<?php

namespace App\Tests\Flow\Base;

use App\Tests\Flow\EndpointTester;
use Symfony\Component\HttpFoundation\Response;

class CheckTest extends EndpointTester
{
    /**
     * @return void
     */
    public function testCall(): void
    {
        $expected = [
            'status' => [
                'success' => true,
                'errors' => [],
            ],
            'data' => [
                'checks' => [
                    'DB - local connection check' => [
                        'success' => true,
                        'message' => 'connection established successfully',
                    ],
                    'DB - local query check' => [
                        'success' => true,
                        'message' => 'base query executed successfully',
                    ],
                ],
            ],
        ];

        $this->client->request('GET', '/__check');
        $response = $this->client->getResponse();
        $responseBody = json_decode((string) $response->getContent(), true);

        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
        $this->assertEquals($expected, $responseBody);

        $this->assertLogSuccess('base.check', Response::HTTP_OK);
    }
}
