<?php

namespace App\Service;

class HasherService
{
    /** @var string */
    public const DEFAULT_HASH_ALGORITHM = 'sha512';

    /** @var int */
    public const DEFAULT_HASH_PASSES = 256;

    /** @var int */
    public const DEFAULT_STRING_LENGTH = 64;

    /**
     * @param string $password
     * @param string $salt
     * @param string $algorithm
     * @param int $passes
     * @return string
     */
    public function hash(
        string $password,
        string $salt = '',
        string $algorithm = self::DEFAULT_HASH_ALGORITHM,
        int $passes = self::DEFAULT_HASH_PASSES,
    ): string {
        // salt the password
        $password .= !empty($salt) ? '.{' . $salt . '}' : '';

        // hashing cycle
        for ($i = 0; $i < $passes; $i++) {
            $password = hash($algorithm, $password);
        }
        return $password;
    }

    /**
     * @param int $length
     * @return string
     */
    public function string(int $length = self::DEFAULT_STRING_LENGTH): string
    {
        // initialize the pool of characters
        $possible = array_merge(range('a', 'z'), range('A', 'Z'), range('0', '9'));
        $possibleSize = count($possible);

        // generate the random string
        $result = '';
        for ($i = 0; $i < $length; ++$i) {
            $result .= $possible[rand(0, $possibleSize - 1)];
        }

        return $result;
    }
}
