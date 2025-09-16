-- Custom Payment Gateway Setup SQL
-- Run this SQL script to add the new payment gateway to your database

-- Insert the gateway
INSERT INTO `gateways` (`code`, `name`, `alias`, `image`, `gateway_parameters`, `supported_currencies`, `extra`, `input_form`, `guideline`, `countries`, `status`, `created_at`, `updated_at`) 
VALUES (
    'custom_gateway',
    'Custom Payment Gateway',
    'customgateway',
    'custom-gateway.png',
    '{"merchant_id":{"title":"Merchant ID","global":true,"value":""},"password":{"title":"Password","global":true,"value":""},"hash_key":{"title":"Hash Key","global":true,"value":""},"return_url":{"title":"Return URL","global":true,"value":""},"sandbox":{"title":"Sandbox Mode","global":true,"value":"0"}}',
    '["USD","EUR","GBP","PKR"]',
    NULL,
    NULL,
    'Configure your custom payment gateway with the provided credentials. Enable sandbox mode for testing.',
    NULL,
    1,
    NOW(),
    NOW()
);

-- Insert the gateway currency for USD
INSERT INTO `gateway_currencies` (`name`, `currency`, `method_code`, `gateway_parameter`, `min_amount`, `max_amount`, `fixed_charge`, `percent_charge`, `rate`, `symbol`, `status`, `created_at`, `updated_at`)
VALUES (
    'Custom Gateway - USD',
    'USD',
    'custom_gateway',
    '{"merchant_id":"","password":"","hash_key":"","return_url":"","sandbox":"0"}',
    1.00,
    10000.00,
    0.00,
    2.50,
    1.00,
    '$',
    1,
    NOW(),
    NOW()
);
