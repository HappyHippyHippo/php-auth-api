<?php

namespace App\Tests\Unit\Transformer\Logging\Decorator;

use App\Transformer\Logging\Decorator\PasswordObfuscationDecorator;
use Exception;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/** @coversDefaultClass \App\Transformer\Logging\Decorator\PasswordObfuscationDecorator */
class PasswordObfuscationDecoratorTest extends TestCase
{
    /**
     * @param array<string, mixed> $data
     * @param array<string, mixed> $expected
     * @return void
     * @covers ::request
     * @covers ::decorate
     * @dataProvider providerForDecorationTests
     */
    public function testRequestWithDefaultPlaceholder(array $data, array $expected): void
    {
        $request = $this->createMock(Request::class);

        $sut = new PasswordObfuscationDecorator();
        $this->assertEquals($expected, $sut->request($data, $request));
    }

    /**
     * @param string $placeholder
     * @param array<string, mixed> $data
     * @param array<string, mixed> $expected
     * @return void
     * @covers ::__construct
     * @covers ::request
     * @covers ::decorate
     * @dataProvider providerForDecorationWithPlaceholderTests
     */
    public function testRequestWithDefinedPlaceholder(string $placeholder, array $data, array $expected): void
    {
        $request = $this->createMock(Request::class);

        $sut = new PasswordObfuscationDecorator($placeholder);
        $this->assertEquals($expected, $sut->request($data, $request));
    }

    /**
     * @param array<string, mixed> $data
     * @param array<string, mixed> $expected
     * @return void
     * @covers ::response
     * @covers ::decorate
     * @dataProvider providerForDecorationTests
     */
    public function testResponseWithDefaultPlaceholder(array $data, array $expected): void
    {
        $request = $this->createMock(Request::class);
        $response = $this->createMock(Response::class);

        $sut = new PasswordObfuscationDecorator();
        $this->assertEquals($expected, $sut->response($data, $request, $response));
    }

    /**
     * @param string $placeholder
     * @param array<string, mixed> $data
     * @param array<string, mixed> $expected
     * @return void
     * @covers ::__construct
     * @covers ::response
     * @covers ::decorate
     * @dataProvider providerForDecorationWithPlaceholderTests
     */
    public function testResponseWithDefinedPlaceholder(string $placeholder, array $data, array $expected): void
    {
        $request = $this->createMock(Request::class);
        $response = $this->createMock(Response::class);

        $sut = new PasswordObfuscationDecorator($placeholder);
        $this->assertEquals($expected, $sut->response($data, $request, $response));
    }

    /**
     * @param array<string, mixed> $data
     * @param array<string, mixed> $expected
     * @return void
     * @covers ::exception
     * @covers ::decorate
     * @dataProvider providerForDecorationTests
     */
    public function testExceptionWithDefaultPlaceholder(array $data, array $expected): void
    {
        $request = $this->createMock(Request::class);
        $exception = new Exception();

        $sut = new PasswordObfuscationDecorator();
        $this->assertEquals($expected, $sut->exception($data, $request, $exception));
    }

    /**
     * @param string $placeholder
     * @param array<string, mixed> $data
     * @param array<string, mixed> $expected
     * @return void
     * @covers ::__construct
     * @covers ::exception
     * @covers ::decorate
     * @dataProvider providerForDecorationWithPlaceholderTests
     */
    public function testExceptionWithDefinedPlaceholder(string $placeholder, array $data, array $expected): void
    {
        $request = $this->createMock(Request::class);
        $exception = new Exception();

        $sut = new PasswordObfuscationDecorator($placeholder);
        $this->assertEquals($expected, $sut->exception($data, $request, $exception));
    }

    /**
     * @return array<string, array<string, mixed>>
     */
    public function providerForDecorationTests(): array
    {
        return [
            'No op if request is missing' => [
                'data' => [
                    'password' => '__dummy_value__'
                ],
                'expected' => [
                    'password' => '__dummy_value__'
                ],
            ],
            'No op if inner request is missing' => [
                'data' => [
                    'password' => '__dummy_value__',
                    'request' => [
                        'password' => '__dummy_value__'
                    ],
                ],
                'expected' => [
                    'password' => '__dummy_value__',
                    'request' => [
                        'password' => '__dummy_value__'
                    ],
                ],
            ],
            'with data to decorate' => [
                'data' => [
                    'password' => '__dummy_value__',
                    'request' => [
                        'password' => '__dummy_value__',
                        'request' => [
                            'password' => '__dummy_value__'
                        ],
                    ],
                    'response' => [
                        'password' => '__dummy_value__',
                        'request' => [
                            'password' => '__dummy_value__'
                        ],
                    ],
                ],
                'expected' => [
                    'password' => '__dummy_value__',
                    'request' => [
                        'password' => '__dummy_value__',
                        'request' => [
                            'password' => '******'
                        ],
                    ],
                    'response' => [
                        'password' => '__dummy_value__',
                        'request' => [
                            'password' => '__dummy_value__'
                        ],
                    ],
                ],
            ],
        ];
    }

    /**
     * @return array<string, array<string, mixed>>
     */
    public function providerForDecorationWithPlaceholderTests(): array
    {
        return [
            'No op if request is missing' => [
                'placeholder' => '______',
                'data' => [
                    'password' => '__dummy_value__'
                ],
                'expected' => [
                    'password' => '__dummy_value__'
                ],
            ],
            'No op if inner request is missing' => [
                'placeholder' => '______',
                'data' => [
                    'password' => '__dummy_value__',
                    'request' => [
                        'password' => '__dummy_value__'
                    ],
                ],
                'expected' => [
                    'password' => '__dummy_value__',
                    'request' => [
                        'password' => '__dummy_value__'
                    ],
                ],
            ],
            'with data to decorate' => [
                'placeholder' => '______',
                'data' => [
                    'password' => '__dummy_value__',
                    'request' => [
                        'password' => '__dummy_value__',
                        'request' => [
                            'password' => '__dummy_value__'
                        ],
                    ],
                    'response' => [
                        'password' => '__dummy_value__',
                        'request' => [
                            'password' => '__dummy_value__'
                        ],
                    ],
                ],
                'expected' => [
                    'password' => '__dummy_value__',
                    'request' => [
                        'password' => '__dummy_value__',
                        'request' => [
                            'password' => '______'
                        ],
                    ],
                    'response' => [
                        'password' => '__dummy_value__',
                        'request' => [
                            'password' => '__dummy_value__'
                        ],
                    ],
                ],
            ],
        ];
    }
}
