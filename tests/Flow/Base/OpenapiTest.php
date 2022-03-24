<?php

namespace App\Tests\Flow\Base;

use App\Tests\Flow\EndpointTester;
use Symfony\Component\HttpFoundation\Response;

class OpenapiTest extends EndpointTester
{
    /**
     * @return void
     */
    public function testCall(): void
    {
        $this->client->request('GET', '/__openapi');
        $response = $this->client->getResponse();

        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
        $this->assertTrue($response->headers->has('Content-Type'));
        $this->assertEquals('text/vnd.yaml; charset=UTF-8', $response->headers->get('Content-Type'));

        $this->assertLogSuccess('base.openapi', Response::HTTP_OK);
    }
}
