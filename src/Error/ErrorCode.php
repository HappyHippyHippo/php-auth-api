<?php

namespace App\Error;

use Hippy\Api\Error\ErrorCode as BaseErrorCode;

abstract class ErrorCode extends BaseErrorCode
{
    public const USER_NOT_FOUND = 10;
    public const USER_NOT_ACTIVE = 11;
    public const USER_MISSING_AUTH = 12;
    public const USER_INVALID_AUTH = 13;
    public const USER_IN_COOL_DOWN = 14;
    public const USER_DUPLICATE_EMAIL = 22;

    public const INVALID_CHALLENGE = 15;
    public const INVALID_RESPONSE = 16;
    public const INVALID_RECOVER = 17;
    public const INVALID_LAST_TOKEN = 18;
    public const TOKEN_STILL_ACTIVE = 19;
    public const TOKEN_NOT_FOUND = 20;
    public const TOKEN_EXPIRED = 21;

    public const ROLE_NOT_FOUND = 23;
    public const DUPLICATE_ROLE_NAME = 24;
    public const ROLE_PERMISSION_NOT_FOUND = 25;
    public const DUPLICATE_ROLE_PERMISSION_DIRECTORY = 26;
    public const USER_PERMISSION_NOT_FOUND = 27;
    public const DUPLICATE_USER_PERMISSION_DIRECTORY = 28;
    public const USER_ROLE_NOT_FOUND = 29;
    public const DUPLICATE_USER_ROLE = 30;

    public const ERROR_TO_MESSAGE = [
        self::UNKNOWN => parent::ERROR_TO_MESSAGE[parent::UNKNOWN],
        self::MALFORMED_JSON => parent::ERROR_TO_MESSAGE[parent::MALFORMED_JSON],
        self::NOT_ENABLED => parent::ERROR_TO_MESSAGE[parent::NOT_ENABLED],
        self::NOT_ALLOWED => parent::ERROR_TO_MESSAGE[parent::NOT_ALLOWED],

        self::USER_NOT_FOUND => 'user not found',
        self::USER_NOT_ACTIVE => 'user not active',
        self::USER_MISSING_AUTH => 'user authentication missing',
        self::USER_INVALID_AUTH => 'invalid authentication information',
        self::USER_IN_COOL_DOWN => 'user in cool down',
        self::INVALID_CHALLENGE => 'invalid challenge',
        self::INVALID_RESPONSE => 'invalid response',
        self::INVALID_RECOVER => 'invalid recover',
        self::INVALID_LAST_TOKEN => 'invalid last token',
        self::TOKEN_STILL_ACTIVE => 'token still active',
        self::TOKEN_NOT_FOUND => 'token not found',
        self::TOKEN_EXPIRED => 'token expired',

        self::USER_DUPLICATE_EMAIL => 'user email already existent in the database',
        self::ROLE_NOT_FOUND => 'role not found',
        self::DUPLICATE_ROLE_NAME => 'role name already existent in the database',
        self::ROLE_PERMISSION_NOT_FOUND => 'role permission not found',
        self::DUPLICATE_ROLE_PERMISSION_DIRECTORY => 'role permission directory already existent in the database',
        self::USER_PERMISSION_NOT_FOUND => 'user permission not found',
        self::DUPLICATE_USER_PERMISSION_DIRECTORY => 'user permission directory already existent in the database',
        self::USER_ROLE_NOT_FOUND => 'user role not found',
        self::DUPLICATE_USER_ROLE => 'user role relation already existent in the database',
    ];
}
