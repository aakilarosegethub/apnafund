-- MWallet Payment Gateway Setup SQL
-- Run this SQL script to add the MWallet payment gateway to your database

-- Insert the gateway
INSERT INTO `gateways` (`code`, `name`, `alias`, `image`, `gateway_parameters`, `supported_currencies`, `extra`, `input_form`, `guideline`, `countries`, `status`, `created_at`, `updated_at`) 
VALUES (
    'mwallet',
    'MWallet Payment Gateway',
    'mwallet',
    'mwallet.png',
    '{"merchant_id":{"title":"Merchant ID","global":true,"value":""},"api_key":{"title":"API Key","global":true,"value":""},"secret_key":{"title":"Secret Key","global":true,"value":""},"sandbox":{"title":"Sandbox Mode","global":true,"value":"0"}}',
    '["USD","PKR","EUR","GBP"]',
    NULL,
    NULL,
    'Configure your MWallet payment gateway with the provided credentials. Enable sandbox mode for testing. Make sure to set up your webhook URL in MWallet dashboard.',
    '["PK","US","GB","CA","AU","DE","FR","IT","ES","NL","BE","AT","CH","SE","NO","DK","FI","IE","PT","GR","LU","CY","MT","SI","SK","CZ","HU","PL","LT","LV","EE","BG","RO","HR","BG","RO","HR","BG","RO","HR"]',
    1,
    NOW(),
    NOW()
);

-- Insert the gateway currency for PKR
INSERT INTO `gateway_currencies` (`name`, `currency`, `method_code`, `gateway_parameter`, `min_amount`, `max_amount`, `fixed_charge`, `percent_charge`, `rate`, `symbol`, `status`, `created_at`, `updated_at`)
VALUES (
    'MWallet - PKR',
    'PKR',
    'mwallet',
    '{"merchant_id":"","api_key":"","secret_key":"","sandbox":"0"}',
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

-- Insert the gateway currency for USD
INSERT INTO `gateway_currencies` (`name`, `currency`, `method_code`, `gateway_parameter`, `min_amount`, `max_amount`, `fixed_charge`, `percent_charge`, `rate`, `symbol`, `status`, `created_at`, `updated_at`)
VALUES (
    'MWallet - USD',
    'USD',
    'mwallet',
    '{"merchant_id":"","api_key":"","secret_key":"","sandbox":"0"}',
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

-- Insert the gateway currency for EUR
INSERT INTO `gateway_currencies` (`name`, `currency`, `method_code`, `gateway_parameter`, `min_amount`, `max_amount`, `fixed_charge`, `percent_charge`, `rate`, `symbol`, `status`, `created_at`, `updated_at`)
VALUES (
    'MWallet - EUR',
    'EUR',
    'mwallet',
    '{"merchant_id":"","api_key":"","secret_key":"","sandbox":"0"}',
    1.00,
    10000.00,
    0.00,
    2.50,
    1.00,
    '€',
    1,
    NOW(),
    NOW()
);

-- Insert the gateway currency for GBP
INSERT INTO `gateway_currencies` (`name`, `currency`, `method_code`, `gateway_parameter`, `min_amount`, `max_amount`, `fixed_charge`, `percent_charge`, `rate`, `symbol`, `status`, `created_at`, `updated_at`)
VALUES (
    'MWallet - GBP',
    'GBP',
    'mwallet',
    '{"merchant_id":"","api_key":"","secret_key":"","sandbox":"0"}',
    1.00,
    10000.00,
    0.00,
    2.50,
    1.00,
    '£',
    1,
    NOW(),
    NOW()
);
