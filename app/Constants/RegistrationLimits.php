<?php

namespace App\Constants;

class RegistrationLimits
{
    /** Maximum length for a single full-name field (OTP / simple signup). */
    public const NAME_MAX = 100;

    /** Maximum length for firstname / lastname fields (classic signup). */
    public const NAME_PART_MAX = 40;

    /** Bcrypt-safe maximum password length. */
    public const PASSWORD_MAX = 72;

    public const PASSWORD_MIN = 8;
}
