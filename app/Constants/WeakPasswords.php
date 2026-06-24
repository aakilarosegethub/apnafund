<?php

namespace App\Constants;

class WeakPasswords
{
    /** @var list<string> */
    public const EXACT = [
        '123', '1234', '12345', '123456', '1234567', '12345678', '123456789', '1234567890',
        '111111', '11111111', '000000', '00000000', '654321', '123123', '121212', '112233',
        'password', 'password1', 'password12', 'password123', 'password1234',
        'pass', 'pass123', 'pass1234', 'passw0rd', 'passw0rd1',
        'abc', 'abc123', 'abc1234', 'abc12345', 'abcdef', 'abcdefgh',
        'qwerty', 'qwerty1', 'qwerty12', 'qwerty123', 'qwertyui', 'qwertyuiop',
        'asdfgh', 'asdfghjk', 'zxcvbn', 'zxcvbnm',
        'admin', 'admin123', 'administrator', 'root', 'root123',
        'letmein', 'welcome', 'welcome1', 'welcome123',
        'iloveyou', 'dragon', 'master', 'monkey', 'login', 'test', 'guest', 'user', 'user123',
        'football', 'baseball', 'superman', 'batman', 'shadow', 'sunshine', 'princess', 'starwars',
        'trustno1', 'access', 'hello', 'hello123', 'changeme', 'default', 'secret', 'secret123',
    ];

    /** Weak bases that are rejected when followed only by digits (e.g. password123). */
    private const WEAK_BASES = [
        'password', 'passw0rd', 'qwerty', 'abc', 'admin', 'welcome', 'letmein', 'login', 'hello',
    ];

    public static function isTooCommon(string $password): bool
    {
        $lower = strtolower(trim($password));

        if ($lower === '') {
            return false;
        }

        if (in_array($lower, self::EXACT, true)) {
            return true;
        }

        foreach (self::WEAK_BASES as $base) {
            if ($lower === $base || preg_match('/^' . preg_quote($base, '/') . '\d*$/', $lower) === 1) {
                return true;
            }
        }

        if (preg_match('/^\d{8,}$/', $lower) === 1) {
            return true;
        }

        if (preg_match('/^[a-z]{8,}$/i', $lower) === 1) {
            return true;
        }

        if (preg_match('/^(.)\1{3,}$/', $password) === 1) {
            return true;
        }

        if (self::isSequentialDigits($lower) || self::isSequentialLetters($lower)) {
            return true;
        }

        return false;
    }

    /** @return list<string> */
    public static function listForFrontend(): array
    {
        return self::EXACT;
    }

    private static function isSequentialDigits(string $value): bool
    {
        if (!preg_match('/^\d+$/', $value) || strlen($value) < 6) {
            return false;
        }

        $digits = str_split($value);
        $ascending = true;
        $descending = true;

        for ($i = 1, $len = count($digits); $i < $len; $i++) {
            if ((int) $digits[$i] !== (int) $digits[$i - 1] + 1) {
                $ascending = false;
            }
            if ((int) $digits[$i] !== (int) $digits[$i - 1] - 1) {
                $descending = false;
            }
        }

        return $ascending || $descending;
    }

    private static function isSequentialLetters(string $value): bool
    {
        if (!preg_match('/^[a-z]+$/', $value) || strlen($value) < 6) {
            return false;
        }

        $chars = str_split($value);
        $ascending = true;
        $descending = true;

        for ($i = 1, $len = count($chars); $i < $len; $i++) {
            if (ord($chars[$i]) !== ord($chars[$i - 1]) + 1) {
                $ascending = false;
            }
            if (ord($chars[$i]) !== ord($chars[$i - 1]) - 1) {
                $descending = false;
            }
        }

        return $ascending || $descending;
    }
}
