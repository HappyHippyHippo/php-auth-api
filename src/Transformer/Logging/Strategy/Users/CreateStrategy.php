<?php

namespace App\Transformer\Logging\Strategy\Users;

use App\Transformer\Logging\Decorator\PasswordObfuscationDecorator;
use Hippy\Api\Transformer\Logging\Decorator\HeaderCleanerDecorator;
use Hippy\Api\Transformer\Logging\Decorator\InjectRequestDeltaDecorator;
use Hippy\Api\Transformer\Logging\Decorator\InjectResponseBodyDecorator;
use Hippy\Api\Transformer\Logging\Strategy\AbstractStrategy;
use Symfony\Component\HttpFoundation\Response;

class CreateStrategy extends AbstractStrategy
{
    public function __construct()
    {
        parent::__construct(
            'users.create',
            [
                new HeaderCleanerDecorator(),
                new InjectRequestDeltaDecorator(),
                new InjectResponseBodyDecorator(Response::HTTP_CREATED),
                new PasswordObfuscationDecorator(),
            ]
        );
    }
}
