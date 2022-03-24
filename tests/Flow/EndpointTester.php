<?php

namespace App\Tests\Flow;

use App\Tests\EndpointTester as BaseEndpointTester;
use Monolog\Handler\TestHandler;

abstract class EndpointTester extends BaseEndpointTester
{
    /** @var string */
    protected const REQUEST_ID = '__dummy_request_id__';

    /** @var int */
    protected const AUTH_TOKEN_ID = 123;

    /** @var int */
    protected const AUTH_USER_ID = 1;

    /** @var string */
    protected const AUTH_USER_EMAIL = 'email@email.com';

    /** @var array<string, mixed> */
    protected const HEADERS = [
        'HTTP_x-request-id' => self::REQUEST_ID,
        'HTTP_x-auth-token-id' => self::AUTH_TOKEN_ID,
        'HTTP_x-auth-user-id' => self::AUTH_USER_ID,
        'HTTP_x-auth-user-email' => self::AUTH_USER_EMAIL,
    ];

    /**
     * @return void
     */
    protected function setUp(): void
    {
        putenv('HIPPY_ENDPOINT_CONFIG_ENABLED=true');
        putenv('HIPPY_ENDPOINT_OPENAPI_ENABLED=true');

        parent::setUp();
    }

    /**
     * @return void
     */
    protected function tearDown(): void
    {
        parent::tearDown();

        putenv('HIPPY_ENDPOINT_CONFIG_ENABLED=');
        putenv('HIPPY_ENDPOINT_OPENAPI_ENABLED=');
    }

    /**
     * @param string $route
     * @param int $statusCode
     * @param mixed $body
     * @param array<int, array<int, mixed>> $checks
     * @return void
     */
    protected function assertLogFailure(string $route, int $statusCode, mixed $body, array $checks = []): void
    {
        $logger = static::getContainer()->get('monolog.handler.main');
        if (!($logger instanceof TestHandler)) {
            $this->fail('unable to retrieve the log test handler');
        }

        $records = $logger->getRecords();
        $this->assertCount(2, $records);

        $this->assertLog($route, $records, $statusCode);
        $this->assertLogResponseBody($records[1], $body);

        foreach ($checks as $check) {
            $callback = [$check[0], $check[1]];
            if (is_callable($callback)) {
                array_shift($check);
                array_shift($check);
                $callback($records, ...$check);
            }
        }
    }

    /**
     * @param string $route
     * @param int $statusCode
     * @param array<int, array<int, mixed>> $checks
     * @return void
     */
    protected function assertLogSuccess(string $route, int $statusCode, array $checks = []): void
    {
        $logger = static::getContainer()->get('monolog.handler.main');
        if (!($logger instanceof TestHandler)) {
            $this->fail('unable to retrieve the log test handler');
        }

        $records = $logger->getRecords();
        $this->assertCount(2, $records);

        $this->assertLog($route, $records, $statusCode);
        $this->assertLogResponseNoBody($records[1]);

        foreach ($checks as $check) {
            $callback = [$check[0], $check[1]];
            if (is_callable($callback)) {
                array_shift($check);
                array_shift($check);
                $callback($records, ...$check);
            }
        }
    }

    /**
     * @param string $route
     * @param array<int, array<string, mixed>> $records
     * @param int $statusCode
     * @return void
     */
    protected function assertLog(string $route, array $records, int $statusCode): void
    {
        $record = $records[0];
        $this->assertLogRequest($record);
        $this->assertLogRequestAttr($record, '_route', $route);

        $record = $records[1];
        $this->assertLogResponse($record);
        $this->assertLogRequestAttr($record, '_route', $route);
        $this->assertLogResponseStatus($record, $statusCode);
    }

    /**
     * @param array<string, mixed> $record
     * @return void
     */
    protected function assertLogRequest(array $record): void
    {
        $this->assertEquals('INFO', $record['level_name']);
        $this->assertEquals('app', $record['channel']);
        $this->assertEquals('Request', $record['message']);
        $this->assertArrayHasKey('request', $record['context']);
        $this->assertArrayHasKey('uri', $record['context']['request']);
        $this->assertArrayHasKey('method', $record['context']['request']);
        $this->assertArrayHasKey('clientIp', $record['context']['request']);
        $this->assertArrayHasKey('headers', $record['context']['request']);
        $this->assertArrayHasKey('query', $record['context']['request']);
        $this->assertArrayHasKey('request', $record['context']['request']);
        $this->assertArrayHasKey('attributes', $record['context']['request']);
    }

    /**
     * @param array<string, mixed> $record
     * @param string $field
     * @param mixed $value
     * @return void
     */
    protected function assertLogRequestAttr(array $record, string $field, mixed $value): void
    {
        $this->assertArrayHasKey($field, $record['context']['request']['attributes']);
        $this->assertEquals($value, $record['context']['request']['attributes'][$field]);
    }

    /**
     * @param array<string, mixed> $record
     * @return void
     */
    protected function assertLogResponse(array $record): void
    {
        $this->assertEquals('INFO', $record['level_name']);
        $this->assertEquals('app', $record['channel']);
        $this->assertEquals('Response', $record['message']);
        $this->assertArrayHasKey('request', $record['context']);
        $this->assertArrayHasKey('headers', $record['context']['request']);
        $this->assertArrayHasKey('query', $record['context']['request']);
        $this->assertArrayHasKey('request', $record['context']['request']);
        $this->assertArrayHasKey('attributes', $record['context']['request']);
        $this->assertArrayHasKey('response', $record['context']);
        $this->assertArrayHasKey('status', $record['context']['response']);
        $this->assertArrayHasKey('headers', $record['context']['response']);
    }

    /**
     * @param array<string, mixed> $record
     * @param int $statusCode
     * @return void
     */
    protected function assertLogResponseStatus(array $record, int $statusCode): void
    {
        $this->assertEquals($statusCode, $record['context']['response']['status']);
    }

    /**
     * @param array<string, mixed> $record
     * @param mixed $body
     * @return void
     */
    protected function assertLogResponseBody(array $record, mixed $body): void
    {
        $this->assertArrayHasKey('body', $record['context']['response']);
        $this->assertEquals($body, $record['context']['response']['body']);
    }

    /**
     * @param array<string, mixed> $record
     * @return void
     */
    protected function assertLogResponseNoBody(array $record): void
    {
        $this->assertArrayNotHasKey('body', $record['context']['response']);
    }

    /**
     * @param array<string, mixed> $record
     * @param string $field
     * @param mixed $value
     * @return void
     */
    protected function assertLogRequestQuery(array $record, string $field, mixed $value): void
    {
        $this->assertArrayHasKey($field, $record['context']['request']['query']);
        $this->assertEquals($value, $record['context']['request']['query'][$field]);
    }

    /**
     * @param array<string, mixed> $record
     * @param mixed $value
     * @return void
     */
    protected function assertLogRequestBody(array $record, mixed $value): void
    {
        $this->assertEquals($value, $record['context']['request']['request']);
    }
}
