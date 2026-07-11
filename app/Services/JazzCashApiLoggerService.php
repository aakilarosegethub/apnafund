<?php

namespace App\Services;

use App\Models\Deposit;
use App\Models\WebhookLog;
use Illuminate\Http\Request;

/**
 * Logs every JazzCash outbound API call, form redirect, and inbound IPN/callback for admin review.
 */
class JazzCashApiLoggerService
{
    private const FILE_LOG_NAME = 'jazzcash-log.txt';

    private const SENSITIVE_KEYS = [
        'pp_Password',
        'Password',
        'pp_SecureHash',
        'Hash',
        'HashKey',
        'hash_key',
        'integrity_salt',
        'password',
        'pp_CNIC',
        'cnic_last_6',
    ];

    /**
     * Payload prepared when /user/deposit/confirm loads (before user submits wallet form).
     */
    public function logDepositConfirmPrepare(
        string $flow,
        string $url,
        array $payload,
        ?Deposit $deposit = null,
        ?string $note = null
    ): void {
        $masked = $this->maskSensitive($payload);
        $curl = $this->buildCurlCommand($url, 'POST', ['Content-Type' => 'application/json'], $masked);

        $this->writeFileLog(
            $this->buildFileLogTitle($flow, 'deposit_confirm', $deposit, null, 'pending'),
            [
                'JAZZCASH_API_URL' => $url,
                'HTTP_METHOD' => 'POST',
                'NOTE' => $note ?? 'Prepared on /user/deposit/confirm',
                'CURL' => $curl,
                'REQUEST' => $masked,
                'RESPONSE' => 'Awaiting wallet form submit or JazzCash redirect',
            ]
        );
    }

    /**
     * Outbound HTTP call to JazzCash API (e.g. DoMWalletTransaction).
     */
    public function logOutboundRequest(
        string $flow,
        string $url,
        string $method,
        array $payload,
        array $headers,
        ?string $responseBody,
        ?int $httpCode,
        ?float $executionTime = null,
        ?Deposit $deposit = null,
        ?string $curlError = null
    ): ?WebhookLog {
        $maskedPayload = $this->maskSensitive($payload);
        $curl = $this->buildCurlCommand($url, $method, $headers, $maskedPayload);

        $status = 'pending';
        if ($curlError !== null) {
            $status = 'failed';
        } elseif ($httpCode !== null) {
            $status = ($httpCode >= 200 && $httpCode < 300) ? 'success' : 'failed';
        }

        $this->writeFileLog(
            $this->buildFileLogTitle($flow, 'outbound', $deposit, $httpCode, $status),
            [
                'JAZZCASH_API_URL' => $url,
                'HTTP_METHOD' => strtoupper($method),
                'CURL' => $curl,
                'REQUEST' => $maskedPayload,
                'RESPONSE' => $responseBody,
                'EXECUTION_TIME_SEC' => $executionTime !== null ? round($executionTime, 4) : null,
                'ERROR' => $curlError,
            ]
        );

        return $this->createLog([
            'flow' => $flow,
            'direction' => 'outbound',
            'url' => $url,
            'method' => strtoupper($method),
            'headers' => $headers,
            'payload' => $maskedPayload,
            'curl_command' => $curl,
            'response_status' => $httpCode,
            'response_body' => $responseBody,
            'execution_time' => $executionTime,
            'status' => $status,
            'error_message' => $curlError,
            'deposit' => $deposit,
            'gateway_code' => 'jazzcash_wallet',
        ]);
    }

    /**
     * Browser form POST redirect to JazzCash (card / mobile wallet merchant form).
     */
    public function logFormRedirect(string $flow, string $url, array $formFields, ?Deposit $deposit = null): ?WebhookLog
    {
        $masked = $this->maskSensitive($formFields);
        $curl = $this->buildCurlCommand($url, 'POST', ['Content-Type: application/x-www-form-urlencoded'], $masked, true);

        $this->writeFileLog(
            $this->buildFileLogTitle($flow, 'outbound_form', $deposit, null, 'pending'),
            [
                'CURL' => $curl,
                'REQUEST' => $masked,
                'RESPONSE' => 'Browser redirect — user submits form to JazzCash',
            ]
        );

        return $this->createLog([
            'flow' => $flow,
            'direction' => 'outbound_form',
            'url' => $url,
            'method' => 'POST',
            'headers' => ['Content-Type' => 'application/x-www-form-urlencoded'],
            'payload' => $masked,
            'curl_command' => $curl,
            'response_status' => null,
            'response_body' => 'Browser redirect — user submits form to JazzCash',
            'execution_time' => null,
            'status' => 'pending',
            'error_message' => null,
            'deposit' => $deposit,
        ]);
    }

    /**
     * Local wallet payment page opened (before user enters phone/CNIC).
     */
    public function logWalletInit(array $context, ?Deposit $deposit = null): ?WebhookLog
    {
        $apiUrl = $context['api_url'] ?? '';
        $curl = $apiUrl !== ''
            ? $this->buildCurlCommand(
                $apiUrl,
                'POST',
                ['Content-Type: application/json'],
                $this->maskSensitive($context),
            )."\n# Note: pp_MobileNumber and pp_CNIC are filled when user submits the wallet form."
            : '# Wallet init — API URL not configured';

        $maskedContext = $this->maskSensitive($context);

        $this->writeFileLog(
            $this->buildFileLogTitle('wallet_init', 'outbound_pending', $deposit, null, 'pending'),
            [
                'CURL' => $curl,
                'REQUEST' => $maskedContext,
                'RESPONSE' => 'Awaiting user phone/CNIC on wallet payment page',
            ]
        );

        return $this->createLog([
            'flow' => 'wallet_init',
            'direction' => 'outbound_pending',
            'url' => $apiUrl ?: 'local://jazzcash-wallet-form',
            'method' => 'POST',
            'headers' => ['Content-Type' => 'application/json'],
            'payload' => $maskedContext,
            'curl_command' => $curl,
            'response_status' => null,
            'response_body' => 'Awaiting user phone/CNIC on wallet payment page',
            'execution_time' => null,
            'status' => 'pending',
            'error_message' => null,
            'deposit' => $deposit,
            'gateway_code' => 'jazzcash_wallet',
        ]);
    }

    /**
     * Inbound IPN / callback from JazzCash or internal IPN route.
     *
     * @return array{webhook_log: ?WebhookLog, start_time: float}
     */
    public function logIncoming(Request $request, string $endpoint, string $flow, ?Deposit $deposit = null, string $gatewayCode = 'jazzcash'): array
    {
        $startTime = microtime(true);
        $curl = $this->buildIncomingCurl($request, $endpoint);

        $this->writeFileLog(
            $this->buildFileLogTitle($flow, 'inbound', $deposit, null, 'processing'),
            [
                'INTERNAL_URL' => url($endpoint),
                'HTTP_METHOD' => $request->method(),
                'NOTE' => 'Browser/AJAX hits our server first; server then calls JazzCash API.',
                'CURL' => $curl,
                'REQUEST' => $this->maskSensitive($request->all()),
                'RAW_INPUT' => $request->getContent() !== '' ? $request->getContent() : null,
            ]
        );

        $webhookLog = $this->createLog([
            'flow' => $flow,
            'direction' => 'inbound',
            'url' => $endpoint,
            'method' => $request->method(),
            'headers' => $request->headers->all(),
            'payload' => $request->all(),
            'curl_command' => $curl,
            'response_status' => null,
            'response_body' => null,
            'execution_time' => null,
            'status' => 'processing',
            'error_message' => null,
            'deposit' => $deposit,
            'raw_input' => $request->getContent(),
            'gateway_code' => $gatewayCode,
        ]);

        return [
            'webhook_log' => $webhookLog,
            'start_time' => $startTime,
        ];
    }

    /**
     * Incoming request to our server (e.g. wallet form AJAX before JazzCash API call).
     *
     * @return array{webhook_log: ?WebhookLog, start_time: float}
     */
    public function logInternalRequest(
        Request $request,
        string $flow,
        string $endpoint,
        ?Deposit $deposit = null,
        string $gatewayCode = 'jazzcash_wallet'
    ): array {
        $startTime = microtime(true);
        $curl = $this->buildIncomingCurl($request, $endpoint);

        $maskedPayload = $this->maskSensitive($request->all());

        $this->writeFileLog(
            $this->buildFileLogTitle($flow, 'internal_inbound', $deposit, null, 'processing'),
            [
                'INTERNAL_URL' => url($endpoint),
                'HTTP_METHOD' => $request->method(),
                'NOTE' => 'Browser/AJAX hits our server first; server then calls JazzCash API.',
                'CURL' => $curl,
                'REQUEST' => $maskedPayload,
                'RAW_INPUT' => $request->getContent() !== '' ? $request->getContent() : null,
            ]
        );

        $webhookLog = $this->createLog([
            'flow' => $flow,
            'direction' => 'internal_inbound',
            'url' => $endpoint,
            'method' => $request->method(),
            'headers' => $request->headers->all(),
            'payload' => $maskedPayload,
            'curl_command' => $curl,
            'response_status' => null,
            'response_body' => null,
            'execution_time' => null,
            'status' => 'processing',
            'error_message' => null,
            'deposit' => $deposit,
            'raw_input' => $request->getContent(),
            'gateway_code' => $gatewayCode,
        ]);

        return [
            'webhook_log' => $webhookLog,
            'start_time' => $startTime,
        ];
    }

    public function finalizeLog(
        array $logContext,
        string $status,
        mixed $responseBody,
        ?int $httpStatus = null
    ): void {
        if (! $logContext['webhook_log'] ?? null) {
            return;
        }

        $executionTime = microtime(true) - ($logContext['start_time'] ?? microtime(true));
        $body = is_array($responseBody)
            ? json_encode($responseBody, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT)
            : (string) $responseBody;

        $logContext['webhook_log']->update([
            'status' => $status,
            'response_body' => $body,
            'response_status' => $httpStatus,
            'execution_time' => $executionTime,
            'error_message' => in_array($status, ['failed', 'error'], true) ? $body : null,
        ]);

        $payload = $logContext['webhook_log']->payload ?? [];
        $flow = is_array($payload) ? ($payload['flow'] ?? 'unknown') : 'unknown';
        $direction = is_array($payload) ? ($payload['direction'] ?? 'callback') : 'callback';
        $trx = is_array($payload) ? ($payload['transaction_id'] ?? null) : null;

        $this->writeFileLog(
            date('Y-m-d H:i:s')." | {$flow} | {$direction} | RESPONSE | trx: ".($trx ?: 'n/a')." | status: {$status}",
            [
                'HTTP_STATUS' => $httpStatus,
                'RESPONSE' => $body,
                'EXECUTION_TIME_SEC' => round($executionTime, 4),
            ]
        );
    }

    public function finalizeInbound(array $logContext, string $status, ?string $responseBody, ?int $httpStatus = null): void
    {
        $this->finalizeLog($logContext, $status, $responseBody, $httpStatus);
    }

    /**
     * Write a custom JazzCash entry to project-root jazzcash-log.txt.
     */
    public function appendRootLog(string $title, array $sections): void
    {
        $this->writeFileLog($title, $sections);
    }

    public function buildCurlCommand(string $url, string $method, array $headers, array $data, bool $formEncoded = false): string
    {
        $method = strtoupper($method);
        $lines = ["curl -X {$method} ".escapeshellarg($url)];

        foreach ($headers as $key => $value) {
            if (is_int($key)) {
                $lines[] = '  '.escapeshellarg($key);
            } else {
                $lines[] = '  -H '.escapeshellarg("{$key}: {$value}");
            }
        }

        if ($method !== 'GET' && $data !== []) {
            if ($formEncoded) {
                $pairs = [];
                foreach ($data as $key => $value) {
                    $pairs[] = rawurlencode((string) $key).'='.rawurlencode((string) $value);
                }
                $body = implode('&', $pairs);
            } else {
                $body = json_encode($data, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);
                if (! isset($headers['Content-Type']) && ! in_array('Content-Type: application/json', $headers, true)) {
                    $lines[] = '  -H '.escapeshellarg('Content-Type: application/json');
                }
            }

            $lines[] = '  -d '.escapeshellarg($body);
        }

        return implode(" \\\n", $lines);
    }

    /**
     * Masked outbound API details for error JSON responses / debugging.
     */
    public function buildOutboundDebug(
        string $url,
        string $method,
        array $headers,
        array $payload,
        ?string $responseBody = null,
        ?int $httpCode = null,
        ?string $curlError = null
    ): array {
        $maskedPayload = $this->maskSensitive($payload);

        return [
            'api_url' => $url,
            'http_method' => strtoupper($method),
            'curl_request' => $this->buildCurlCommand($url, $method, $headers, $maskedPayload),
            'request' => $maskedPayload,
            'api_response' => $responseBody,
            'http_status' => $httpCode,
            'curl_error' => $curlError,
        ];
    }

    public function buildIncomingCurl(Request $request, string $endpoint): string
    {
        $fullUrl = url($endpoint);
        $headers = [];
        foreach ($request->headers->all() as $name => $values) {
            $headers[$name] = is_array($values) ? ($values[0] ?? '') : $values;
        }

        $contentType = $request->header('Content-Type', '');
        $raw = $request->getContent();

        if (str_contains($contentType, 'json') && $raw !== '') {
            $decoded = json_decode($raw, true);
            $data = is_array($decoded) ? $this->maskSensitive($decoded) : ['raw' => $raw];

            return $this->buildCurlCommand($fullUrl, $request->method(), $headers, $data);
        }

        if ($request->all() !== []) {
            return $this->buildCurlCommand(
                $fullUrl,
                $request->method(),
                $headers,
                $this->maskSensitive($request->all()),
                true
            );
        }

        if ($raw !== '') {
            return $this->buildCurlCommand($fullUrl, $request->method(), $headers, ['raw_body' => $raw]);
        }

        return $this->buildCurlCommand($fullUrl, $request->method(), $headers, []);
    }

    private function createLog(array $options): ?WebhookLog
    {
        try {
            $deposit = $options['deposit'] ?? null;
            $payload = is_array($options['payload'] ?? null) ? $options['payload'] : [];

            return WebhookLog::create([
                'webhook_type' => 'jazzcash_'.($options['flow'] ?? 'unknown'),
                'url' => $options['url'] ?? '',
                'method' => $options['method'] ?? 'POST',
                'headers' => $options['headers'] ?? [],
                'payload' => array_merge($payload, [
                    'gateway' => 'jazzcash',
                    'gateway_code' => $options['gateway_code'] ?? 'jazzcash',
                    'flow' => $options['flow'] ?? null,
                    'direction' => $options['direction'] ?? null,
                    'curl_command' => $options['curl_command'] ?? null,
                    'transaction_id' => $deposit?->trx
                        ?? ($payload['transaction_id'] ?? null)
                        ?? ($payload['pp_TxnRefNo'] ?? $payload['TransactionID'] ?? $payload['pp_BillReference'] ?? null),
                    'deposit_id' => $deposit?->id,
                    'raw_input' => $options['raw_input'] ?? null,
                ]),
                'response_status' => $options['response_status'] ?? null,
                'response_body' => $options['response_body'] ?? null,
                'response_headers' => null,
                'execution_time' => $options['execution_time'] ?? null,
                'status' => $options['status'] ?? 'pending',
                'error_message' => $options['error_message'] ?? null,
                'retry_count' => 0,
                'user_id' => $deposit?->user_id,
                'campaign_id' => $deposit?->campaign_id,
            ]);
        } catch (\Throwable $e) {
            report($e);

            return null;
        }
    }

    private function maskSensitive(array $data): array
    {
        $masked = [];

        foreach ($data as $key => $value) {
            if (in_array($key, self::SENSITIVE_KEYS, true)) {
                $masked[$key] = '***REDACTED***';
            } elseif (is_array($value)) {
                $masked[$key] = $this->maskSensitive($value);
            } else {
                $masked[$key] = $value;
            }
        }

        return $masked;
    }

    private function fileLogPath(): string
    {
        return base_path(self::FILE_LOG_NAME);
    }

    private function fallbackFileLogPath(): string
    {
        return storage_path('logs/'.self::FILE_LOG_NAME);
    }

    private function resolveWritableLogPath(): string
    {
        $primary = $this->fileLogPath();
        $directory = dirname($primary);

        if (is_dir($directory) && is_writable($directory)) {
            return $primary;
        }

        $fallback = $this->fallbackFileLogPath();
        $fallbackDir = dirname($fallback);

        if (! is_dir($fallbackDir)) {
            @mkdir($fallbackDir, 0775, true);
        }

        return is_writable($fallbackDir) ? $fallback : $primary;
    }

    private function buildFileLogTitle(
        string $flow,
        string $direction,
        ?Deposit $deposit = null,
        ?int $httpCode = null,
        ?string $status = null
    ): string {
        $parts = [
            date('Y-m-d H:i:s'),
            $flow,
            $direction,
        ];

        if ($deposit?->trx) {
            $parts[] = 'trx: '.$deposit->trx;
        }

        if ($httpCode !== null) {
            $parts[] = 'HTTP '.$httpCode;
        }

        if ($status !== null) {
            $parts[] = 'status: '.$status;
        }

        return implode(' | ', $parts);
    }

    /**
     * Append JazzCash request/response details to project-root jazzcash-log.txt.
     */
    private function writeFileLog(string $title, array $sections): void
    {
        try {
            $lines = [
                '',
                str_repeat('=', 90),
                $title,
                str_repeat('=', 90),
            ];

            foreach ($sections as $label => $content) {
                if ($content === null || $content === '') {
                    continue;
                }

                $lines[] = '';
                $lines[] = '--- '.strtoupper((string) $label).' ---';

                if (is_string($content)) {
                    $lines[] = $content;

                    continue;
                }

                if (is_array($content)) {
                    $lines[] = json_encode($content, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);

                    continue;
                }

                $lines[] = (string) $content;
            }

            $lines[] = '';

            $written = false;
            foreach ([$this->fileLogPath(), $this->fallbackFileLogPath()] as $target) {
                $directory = dirname($target);
                if (! is_dir($directory)) {
                    @mkdir($directory, 0775, true);
                }
                if (! is_dir($directory) || ! is_writable($directory)) {
                    continue;
                }
                file_put_contents($target, implode("\n", $lines), FILE_APPEND | LOCK_EX);
                $written = true;
            }

            if (! $written) {
                \Log::channel('payments')->warning('JazzCash file log could not be written', [
                    'paths' => [$this->fileLogPath(), $this->fallbackFileLogPath()],
                ]);
            }
        } catch (\Throwable $e) {
            report($e);
        }
    }

    public function getLogFilePaths(): array
    {
        return array_values(array_unique([
            $this->fileLogPath(),
            $this->fallbackFileLogPath(),
        ]));
    }

    public function getLogFilePath(): string
    {
        return $this->resolveWritableLogPath();
    }
}
