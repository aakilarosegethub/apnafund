# JazzCash IPN Logging System

## Overview
This system logs all incoming data from JazzCash IPN callbacks to the database for debugging and monitoring purposes.

## Features
- Logs all incoming data (GET, POST, PUT, etc.)
- Captures request headers, IP address, user agent
- Stores raw input data
- Tracks transaction status
- Provides comprehensive logging for debugging

## Endpoints

### 1. JazzCash IPN Endpoint
```
URL: https://apnacrowdfunding.com/jazzcash/ipn
Method: ANY (GET, POST, PUT, etc.)
```

This endpoint:
- Accepts any HTTP method
- Logs all incoming data to `data_logs` table
- Processes JazzCash payment callbacks
- Returns appropriate responses

### 2. Test Logging Endpoint
```
URL: https://apnacrowdfunding.com/test-logging
Method: ANY (GET, POST, PUT, etc.)
```

This endpoint:
- Tests the logging functionality
- Logs data to Laravel log file
- Returns JSON response with logged data

## Database Schema

The `data_logs` table includes:
- `id` - Primary key
- `endpoint` - The endpoint that received the request
- `method` - HTTP method (GET, POST, etc.)
- `request_data` - All request parameters (JSON)
- `headers` - Request headers (JSON)
- `ip_address` - Client IP address
- `user_agent` - Client user agent
- `raw_input` - Raw input data
- `transaction_id` - Transaction ID if available
- `status` - Processing status (received, processing, success, failed, error)
- `response` - Response sent back
- `created_at` - Timestamp
- `updated_at` - Last updated timestamp

## Usage

### Testing the Logging
1. Send a request to `/test-logging` with any data:
```bash
curl -X POST https://apnacrowdfunding.com/test-logging \
  -H "Content-Type: application/json" \
  -d '{"test": "data", "amount": 100}'
```

2. Check the response and Laravel logs

### JazzCash IPN
1. Configure JazzCash to send callbacks to `/jazzcash/ipn`
2. All incoming data will be automatically logged
3. Check `data_logs` table for logged data

## Files Created/Modified

1. **Migration**: `database/migrations/2025_09_16_121844_create_data_logs_table.php`
2. **Model**: `app/Models/DataLog.php`
3. **Controller**: `app/Http/Controllers/Gateway/JazzCash/IpnController.php`
4. **Routes**: Added to `routes/web.php`

## Running Migration

To create the `data_logs` table, run:
```bash
php artisan migrate
```

## Monitoring

You can monitor the logs by:
1. Checking the `data_logs` table in your database
2. Viewing Laravel logs in `storage/logs/laravel.log`
3. Using the test endpoint to verify functionality

## Security Notes

- The IPN endpoint is public and accepts any method
- All data is logged for debugging purposes
- Consider implementing rate limiting for production use
- Monitor logs regularly for suspicious activity
