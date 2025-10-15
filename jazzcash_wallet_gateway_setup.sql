-- JazzCash Wallet Payment Gateway Setup SQL
-- Run this SQL script to add the new JazzCash Wallet payment gateway to your database

-- Insert the gateway
INSERT INTO `gateways` (`code`, `name`, `alias`, `gateway_parameters`, `supported_currencies`, `extra`, `guideline`, `countries`, `status`, `created_at`, `updated_at`) 
VALUES (
    999,
    'JazzCash Wallet Payment',
    'jazzcashwallet',
    '{"merchant_id":{"title":"Merchant ID","global":true,"value":""},"password":{"title":"Password","global":true,"value":""},"integrity_salt":{"title":"Integrity Salt","global":true,"value":""},"sandbox":{"title":"Sandbox Mode","global":true,"value":"0"}}',
    '["PKR"]',
    NULL,
    'Configure your JazzCash Wallet payment gateway with the provided credentials. Enable sandbox mode for testing. Users will need to enter their phone number and CNIC last 6 digits for payment processing.',
    '["PK"]',
    1,
    NOW(),
    NOW()
);

-- Insert the gateway currency for PKR
INSERT INTO `gateway_currencies` (`name`, `currency`, `method_code`, `gateway_parameter`, `min_amount`, `max_amount`, `fixed_charge`, `percent_charge`, `rate`, `symbol`, `status`, `created_at`, `updated_at`)
VALUES (
    'JazzCash Wallet - PKR',
    'PKR',
    999,
    '{"merchant_id":"","password":"","integrity_salt":"","sandbox":"0"}',
    100.00,
    1000000.00,
    0.00,
    2.50,
    1.00,
    'Rs',
    1,
    NOW(),
    NOW()
);
