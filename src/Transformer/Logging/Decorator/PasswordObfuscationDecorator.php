<?php

namespace App\Transformer\Logging\Decorator;

use Hippy\Api\Transformer\Logging\Decorator\AbstractDecorator;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class PasswordObfuscationDecorator extends AbstractDecorator
{
    /** @var string */
    public const PLACEHOLDER = '******';

    /**
     * @param string $placeholder
     */
    public function __construct(
        private string $placeholder = self::PLACEHOLDER
    ) {
    }

    /**
     * @param array<string, mixed> $data
     * @param Request|null $request
     * @return array<string, mixed>
     */
    public function request(array $data, ?Request $request = null): array
    {
        return $this->decorate($data);
    }

    /**
     * @param array<string, mixed> $data
     * @param Request|null $request
     * @param Response|null $response
     * @return array<string, mixed>
     */
    public function response(array $data, ?Request $request = null, ?Response $response = null): array
    {
        return $this->decorate($data);
    }

    /**
     * @param array<string, mixed> $data
     * @param Request|null $request
     * @param Throwable|null $exception
     * @return array<string, mixed>
     */
    public function exception(array $data, ?Request $request = null, ?Throwable $exception = null): array
    {
        return $this->decorate($data);
    }

    /**
     * @param array<string, mixed> $data
     * @return array<string, mixed>
     */
    protected function decorate(array $data): array
    {
        if (
            array_key_exists('request', $data)
            && is_array($data['request'])
            && array_key_exists('request', $data['request'])
            && is_array($data['request']['request'])
            && array_key_exists('password', $data['request']['request'])
        ) {
            $data['request']['request']['password'] = $this->placeholder;
        }

        return $data;
    }
}
