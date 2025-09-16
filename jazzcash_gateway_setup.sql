-- JazzCash Mobile Wallet Gateway Setup SQL
-- Mobile wallet payment gateway for Pakistan
-- Run this SQL script to add the JazzCash gateway to your database

-- Insert the JazzCash mobile wallet gateway
INSERT INTO `gateways` (`code`, `name`, `alias`, `image`, `gateway_parameters`, `supported_currencies`, `extra`, `input_form`, `guideline`, `countries`, `status`, `created_at`, `updated_at`) 
VALUES (
    'jazzcash',
    'JazzCash Mobile Wallet',
    'jazzcash',
    'jazzcash-logo.png',
    '{"merchant_id":{"title":"Merchant ID","global":true,"value":""},"password":{"title":"Password","global":true,"value":""},"hash_key":{"title":"Hash Key","global":true,"value":""},"return_url":{"title":"Return URL","global":true,"value":""},"sandbox":{"title":"Sandbox Mode","global":true,"value":"0"}}',
    '["PKR","USD"]',
    NULL,
    NULL,
    'Configure your JazzCash Mobile Wallet gateway with the provided credentials. JazzCash is Pakistan\'s leading mobile wallet service. Enable sandbox mode for testing.',
    '["Pakistan"]',
    1,
    NOW(),
    NOW()
);

-- Insert the gateway currency for PKR (Primary currency for JazzCash)
INSERT INTO `gateway_currencies` (`name`, `currency`, `method_code`, `gateway_parameter`, `min_amount`, `max_amount`, `fixed_charge`, `percent_charge`, `rate`, `symbol`, `status`, `created_at`, `updated_at`)
VALUES (
    'JazzCash - PKR',
    'PKR',
    'jazzcash',
    '{"merchant_id":"","password":"","hash_key":"","return_url":"","sandbox":"0"}',
    50.00,
    100000.00,
    0.00,
    1.50,
    1.00,
    'Rs',
    1,
    NOW(),
    NOW()
);

-- Insert the gateway currency for USD
INSERT INTO `gateway_currencies` (`name`, `currency`, `method_code`, `gateway_parameter`, `min_amount`, `max_amount`, `fixed_charge`, `percent_charge`, `rate`, `symbol`, `status`, `created_at`, `updated_at`)
VALUES (
    'JazzCash - USD',
    'USD',
    'jazzcash',
    '{"merchant_id":"","password":"","hash_key":"","return_url":"","sandbox":"0"}',
    1.00,
    1000.00,
    0.00,
    1.50,
    280.00,
    '$',
    1,
    NOW(),
    NOW()
);

