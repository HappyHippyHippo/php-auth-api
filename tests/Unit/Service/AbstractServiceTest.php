<?php

namespace App\Tests\Unit\Service;

use App\Error\ErrorCode;
use App\Service\AbstractService;
use Hippy\Api\Error\ErrorCode as ErrorCodeAlias;
use Hippy\Exception\Exception;
use PHPUnit\Framework\TestCase;
use ReflectionException;
use ReflectionMethod;

/** @coversDefaultClass \App\Service\AbstractService */
class AbstractServiceTest extends TestCase
{
    /**
     * @return void
     * @throws ReflectionException
     * @covers ::throws
     */
    public function testThrowsUsesDefaultMessageConverterIfNoMessageIsGiven(): void
    {
        $status = 123;
        $error = ErrorCodeAlias::MALFORMED_JSON;
        $expected = [
            [
                'code' => 'c' . $error,
                'message' => ErrorCode::ERROR_TO_MESSAGE[$error]
            ],
        ];

        $method = new ReflectionMethod(AbstractService::class, 'throws');

        try {
            $sut = $this->getMockForAbstractClass(AbstractService::class);
            $method->invoke($sut, $status, $error);
        } catch (Exception $exception) {
            $this->assertEquals($status, $exception->getStatusCode());
            $this->assertEquals($expected, $exception->getErrors()->jsonSerialize());

            return;
        }
    }

    /**
     * @return void
     * @throws ReflectionException
     * @covers ::throws
     */
    public function testThrowsUsesGivenMessage(): void
    {
        $status = 123;
        $error = ErrorCodeAlias::MALFORMED_JSON;
        $message = '__dummy_message__';
        $expected = [
            [
                'code' => 'c' . $error,
                'message' => $message
            ],
        ];

        $method = new ReflectionMethod(AbstractService::class, 'throws');

        try {
            $sut = $this->getMockForAbstractClass(AbstractService::class);
            $method->invoke($sut, $status, $error, $message);
        } catch (Exception $exception) {
            $this->assertEquals($status, $exception->getStatusCode());
            $this->assertEquals($expected, $exception->getErrors()->jsonSerialize());

            return;
        }
    }

    /**
     * @param array<int> $errors
     * @param array<string>|null $messages
     * @param array<int, array<string, mixed>> $expected
     * @return void
     * @throws ReflectionException
     * @covers ::throwsMany
     * @dataProvider providerForThrowsManyTest
     */
    public function testThrowsMany(array $errors, ?array $messages, array $expected): void
    {
        $status = 123;

        $method = new ReflectionMethod(AbstractService::class, 'throwsMany');

        try {
            $sut = $this->getMockForAbstractClass(AbstractService::class);
            $method->invoke($sut, $status, $errors, $messages);
        } catch (Exception $exception) {
            $this->assertEquals($status, $exception->getStatusCode());
            $this->assertEquals($expected, $exception->getErrors()->jsonSerialize());

            return;
        }
    }

    /**
     * @return array<string, array<string, mixed>>
     */
    public function providerForThrowsManyTest(): array
    {
        return [
            'without messages' => [
                'errors' => [ErrorCodeAlias::MALFORMED_JSON, ErrorCodeAlias::NOT_ENABLED],
                'messages' => null,
                'expected' => [
                    [
                        'code' => 'c' . ErrorCodeAlias::MALFORMED_JSON,
                        'message' => ErrorCode::ERROR_TO_MESSAGE[ErrorCodeAlias::MALFORMED_JSON]
                    ],
                    [
                        'code' => 'c' . ErrorCodeAlias::NOT_ENABLED,
                        'message' => ErrorCode::ERROR_TO_MESSAGE[ErrorCodeAlias::NOT_ENABLED]
                    ]
                ]
            ],
            'with messages' => [
                'errors' => [ErrorCodeAlias::MALFORMED_JSON, ErrorCodeAlias::NOT_ENABLED],
                'messages' => [
                    ErrorCodeAlias::MALFORMED_JSON => '__dummy_message__'
                ],
                'expected' => [
                    [
                        'code' => 'c' . ErrorCodeAlias::MALFORMED_JSON,
                        'message' => '__dummy_message__'
                    ],
                    [
                        'code' => 'c' . ErrorCodeAlias::NOT_ENABLED,
                        'message' => ErrorCode::ERROR_TO_MESSAGE[ErrorCodeAlias::NOT_ENABLED]
                    ]
                ]
            ]
        ];
    }
}
