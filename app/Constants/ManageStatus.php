<?php

namespace App\Constants;

class ManageStatus
{
    const ACTIVE   = 1;
    const INACTIVE = 0;

    const YES = 1;
    const NO  = 0;

    const UNVERIFIED = 0;
    const VERIFIED   = 1;
    const PENDING    = 2;

    const PAYMENT_INITIATE = 0;
    const PAYMENT_SUCCESS  = 1;
    const PAYMENT_PENDING  = 2;
    const PAYMENT_CANCEL   = 3;

    const CAMPAIGN_REJECTED = 0;
    const CAMPAIGN_APPROVED = 1;
    const CAMPAIGN_PENDING  = 2;

    const CAMPAIGN_COMMENT_REJECTED = 0;
    const CAMPAIGN_COMMENT_APPROVED = 1;
    const CAMPAIGN_COMMENT_PENDING  = 2;

    const ANONYMOUS_DONOR = 0;
    const KNOWN_DONOR     = 1;
}
