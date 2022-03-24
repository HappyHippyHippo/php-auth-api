<?php

namespace App\Service;

use App\Error\ErrorCode;
use Hippy\Api\Service\AbstractService as BaseAbstractService;
use Hippy\Error\Error;
use Hippy\Error\ErrorCollection;
use Hippy\Exception\Exception;

abstract class AbstractService extends BaseAbstractService
{
    /**
     * @param int $statusCode
     * @param int $error
     * @param string|null $message
     * @return never
     * @throws Exception
     */
    protected function throws(int $statusCode, int $error, ?string $message = null): never
    {
        throw (new Exception($statusCode))->addError(
            new Error($error, $message ?? ErrorCode::ERROR_TO_MESSAGE[$error])
        );
    }

    /**
     * @param int $statusCode
     * @param array<int, int|string> $errors
     * @param array<int, string>|null $messages
     * @return never
     * @throws Exception
     */
    protected function throwsMany(int $statusCode, array $errors, ?array $messages = null): never
    {
        $collection = new ErrorCollection();
        foreach ($errors as $error) {
            $collection->add(new Error($error, $messages[$error] ?? ErrorCode::ERROR_TO_MESSAGE[$error]));
        }

        throw (new Exception($statusCode))->addErrors($collection);
    }
}
