<?php

namespace App\Tests\Flow\Base;

use App\Tests\Flow\EndpointTester;
use Symfony\Component\HttpFoundation\Response;

class ConfigTest extends EndpointTester
{
    /**
     * @return void
     */
    public function testCall(): void
    {
        $this->client->request('GET', '/__config');

        $response = $this->client->getResponse();
        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());

        $this->assertLogSuccess('base.config', Response::HTTP_OK);
    }
}
