-- Card Payment Gateway v1.1 Setup SQL
-- Page Redirection v1.1 template for Card Payments
-- Run this SQL script to add the new card payment gateway to your database

-- Insert the CardPayment gateway
INSERT INTO `gateways` (`code`, `name`, `alias`, `image`, `gateway_parameters`, `supported_currencies`, `extra`, `input_form`, `guideline`, `countries`, `status`, `created_at`, `updated_at`) 
VALUES (
    'card_payment',
    'Card Payment Gateway v1.1',
    'CardPayment',
    'card-payment.png',
    '{"merchant_id":{"title":"Merchant ID","global":true,"value":""},"password":{"title":"Password","global":true,"value":""},"hash_key":{"title":"Hash Key","global":true,"value":""},"return_url":{"title":"Return URL","global":true,"value":""},"sandbox":{"title":"Sandbox Mode","global":true,"value":"0"}}',
    '["USD","EUR","GBP","PKR","CAD","AUD"]',
    NULL,
    NULL,
    'Configure your Card Payment Gateway v1.1 with the provided credentials. This gateway supports Page Redirection v1.1 template for card payments. Enable sandbox mode for testing.',
    NULL,
    1,
    NOW(),
    NOW()
);

-- Insert the gateway currency for USD
INSERT INTO `gateway_currencies` (`name`, `currency`, `method_code`, `gateway_parameter`, `min_amount`, `max_amount`, `fixed_charge`, `percent_charge`, `rate`, `symbol`, `status`, `created_at`, `updated_at`)
VALUES (
    'Card Payment - USD',
    'USD',
    'card_payment',
    '{"merchant_id":"","password":"","hash_key":"","return_url":"","sandbox":"0"}',
    1.00,
    50000.00,
    0.00,
    2.90,
    1.00,
    '$',
    1,
    NOW(),
    NOW()
);

-- Insert the gateway currency for PKR
INSERT INTO `gateway_currencies` (`name`, `currency`, `method_code`, `gateway_parameter`, `min_amount`, `max_amount`, `fixed_charge`, `percent_charge`, `rate`, `symbol`, `status`, `created_at`, `updated_at`)
VALUES (
    'Card Payment - PKR',
    'PKR',
    'card_payment',
    '{"merchant_id":"","password":"","hash_key":"","return_url":"","sandbox":"0"}',
    100.00,
    5000000.00,
    0.00,
    2.90,
    280.00,
    'Rs',
    1,
    NOW(),
    NOW()
);
