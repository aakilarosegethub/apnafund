<?php

namespace App\Services;

use App\Models\EmailLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class EmailLoggingService
{
    /**
     * Log an email that was sent
     */
    public static function logEmail(array $emailData): EmailLog
    {
        try {
            $logData = [
                'to_email' => $emailData['to_email'] ?? '',
                'to_name' => $emailData['to_name'] ?? null,
                'from_email' => $emailData['from_email'] ?? '',
                'from_name' => $emailData['from_name'] ?? null,
                'subject' => $emailData['subject'] ?? '',
                'body' => $emailData['body'] ?? '',
                'template_name' => $emailData['template_name'] ?? null,
                'email_type' => $emailData['email_type'] ?? 'general',
                'status' => $emailData['status'] ?? 'sent',
                'provider' => $emailData['provider'] ?? null,
                'provider_response' => $emailData['provider_response'] ?? null,
                'error_message' => $emailData['error_message'] ?? null,
                'user_id' => $emailData['user_id'] ?? null,
                'ip_address' => $emailData['ip_address'] ?? null,
                'user_agent' => $emailData['user_agent'] ?? null,
                'sent_at' => $emailData['sent_at'] ?? now(),
            ];

            return EmailLog::create($logData);

        } catch (\Exception $e) {
            Log::error('Failed to log email: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Log email from request context
     */
    public static function logEmailFromRequest(array $emailData, Request $request = null): EmailLog
    {
        if ($request) {
            $emailData['ip_address'] = $request->ip();
            $emailData['user_agent'] = $request->userAgent();
        }

        return self::logEmail($emailData);
    }

    /**
     * Log successful email
     */
    public static function logSuccessfulEmail(
        string $toEmail,
        string $subject,
        string $body,
        string $emailType = 'general',
        array $additionalData = []
    ): EmailLog {
        $emailData = array_merge([
            'to_email' => $toEmail,
            'subject' => $subject,
            'body' => $body,
            'email_type' => $emailType,
            'status' => 'sent',
            'sent_at' => now(),
        ], $additionalData);

        return self::logEmail($emailData);
    }

    /**
     * Log email from notification
     */
    public static function logNotificationEmail(
        $user,
        $notification,
        string $emailType = 'notification',
        array $additionalData = []
    ): EmailLog {
        try {
            $mailMessage = $notification->toMail($user);
            
            $emailData = array_merge([
                'to_email' => $user->email,
                'to_name' => $user->firstname . ' ' . $user->lastname,
                'subject' => $mailMessage->subject ?? 'Notification',
                'body' => $mailMessage->render(),
                'email_type' => $emailType,
                'status' => 'sent',
                'sent_at' => now(),
                'user_id' => $user->id,
                'template_name' => class_basename($notification),
                'provider' => 'laravel_mail',
            ], $additionalData);

            return self::logEmail($emailData);

        } catch (\Exception $e) {
            Log::error('Failed to log notification email: ' . $e->getMessage());
            
            // Log as failed email
            return self::logFailedEmail(
                $user->email,
                'Notification',
                'Failed to render email content',
                $e->getMessage(),
                $emailType,
                array_merge($additionalData, ['user_id' => $user->id])
            );
        }
    }

    /**
     * Log failed email
     */
    public static function logFailedEmail(
        string $toEmail,
        string $subject,
        string $body,
        string $errorMessage,
        string $emailType = 'general',
        array $additionalData = []
    ): EmailLog {
        $emailData = array_merge([
            'to_email' => $toEmail,
            'subject' => $subject,
            'body' => $body,
            'email_type' => $emailType,
            'status' => 'failed',
            'error_message' => $errorMessage,
        ], $additionalData);

        return self::logEmail($emailData);
    }

    /**
     * Get email statistics
     */
    public static function getEmailStats(int $days = 30): array
    {
        $startDate = now()->subDays($days);

        return [
            'total_emails' => EmailLog::where('created_at', '>=', $startDate)->count(),
            'sent_emails' => EmailLog::where('created_at', '>=', $startDate)->where('status', 'sent')->count(),
            'failed_emails' => EmailLog::where('created_at', '>=', $startDate)->where('status', 'failed')->count(),
            'welcome_emails' => EmailLog::where('created_at', '>=', $startDate)->where('email_type', 'welcome')->count(),
            'verification_emails' => EmailLog::where('created_at', '>=', $startDate)->where('email_type', 'verification')->count(),
            'notification_emails' => EmailLog::where('created_at', '>=', $startDate)->where('email_type', 'notification')->count(),
        ];
    }
}
