<?php

namespace App\Notify;

use SendGrid;
use Exception;
use Mailjet\Client;
use Mailjet\Resources;
use SendGrid\Mail\Mail;
use App\Notify\Notifiable;
use App\Notify\NotifyProcess;
use App\Services\EmailLoggingService;
use PHPMailer\PHPMailer\PHPMailer;

class Email extends NotifyProcess implements Notifiable
{
	/**
	 * Email of receiver
	 *
	 * @var string
	 */
	public $email;

	/**
	 * Email service response for debugging
	 *
	 * @var array
	 */
	public $response;

	/**
	 * Assign value to properties
	 *
	 * @return void
	 */
	public function __construct()
	{
		$this->statusField    = 'email_status';
		$this->body           = 'email_body';
		$this->globalTemplate = 'email_template';
		$this->notifyConfig   = 'mail_config';
	}

	/**
	 * Send notification
	 *
	 * @return void|bool
	 */
	public function send()
	{
		//get message from parent
		$message = $this->getMessage();

		if ($this->setting->ea && $message) {
			//Send mail
			$methodName = $this->setting->mail_config->name;
			$method     = $this->mailMethods($methodName);

			try {
				$this->$method();
				
				// Log successful email
				$this->logEmailToDatabase('sent');
				
			} catch (Exception $e) {
				$this->createErrorLog($e->getMessage());
				session()->flash('mail_error', $e->getMessage());
				
				// Log failed email
				$this->logEmailToDatabase('failed', $e->getMessage());
			}
		}
	}

	/**
	 * Get the method name
	 *
	 * @return string
	 */
	protected function mailMethods($name)
	{
		$methods = [
			'php'      => 'sendPhpMail',
			'smtp'     => 'sendSmtpMail',
			'sendgrid' => 'sendSendGridMail',
			'mailjet'  => 'sendMailjetMail',
		];

		return $methods[$name];
	}

	protected function sendPhpMail()
	{
		$setting  = $this->setting;
		$headers  = "From: $setting->site_name <$setting->email_from> \r\n";
		$headers .= "Reply-To: $setting->site_name <$setting->email_from> \r\n";
		$headers .= "MIME-Version: 1.0\r\n";
		$headers .= "Content-Type: text/html; charset=utf-8\r\n";

		@mail($this->email, $this->subject, $this->finalMessage, $headers);
	}

	protected function sendSmtpMail()
	{
		$mail    = new PHPMailer(true);
		$setting = $this->setting;
		$config  = $setting->mail_config;

		//Server settings
		$mail->isSMTP();
		$mail->Host     = $config->host;
		$mail->SMTPAuth = true;
		$mail->Username = $config->username;
		$mail->Password = $config->password;

		if ($config->enc == 'ssl') {
			$mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
		} else {
			$mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
		}

		$mail->Port    = $config->port;
		$mail->CharSet = 'UTF-8';

		//Recipients
		$mail->setFrom($setting->email_from, $setting->site_name);
		$mail->addAddress($this->email, $this->receiverName);
		$mail->addReplyTo($setting->email_from, $setting->site_name);

		// Content
		$mail->isHTML(true);
		$mail->Subject = $this->subject;
		$mail->Body    = $this->finalMessage;
		$mail->send();
	}

	protected function sendSendGridMail()
	{
		$setting      = $this->setting;
		$sendgridMail = new Mail();
		$sendgridMail->setFrom($setting->email_from, $setting->site_name);
		$sendgridMail->setSubject($this->subject);
		$sendgridMail->addTo($this->email, $this->receiverName);
		$sendgridMail->addContent("text/html", $this->finalMessage);

		$sendgrid = new SendGrid($setting->mail_config->appkey);
		$response = $sendgrid->send($sendgridMail);

		// Store response for debugging
		$this->response = [
			'status_code' => $response->statusCode(),
			'headers' => $response->headers(),
			'body' => $response->body()
		];

		if ($response->statusCode() != 202) {
			throw new Exception(json_decode($response->body())->errors[0]->message);
		}
	}

	protected function sendMailjetMail()
	{
		$setting = $this->setting;
		$mj      = new Client($setting->mail_config->public_key, $setting->mail_config->secret_key, true, ['version' => 'v3.1']);
		$body    = [
			'Messages' => [
				[
					'From' => [
						'Email' => $setting->email_from,
						'Name'  => $setting->site_name,
					],
					'To'   => [
						[
							'Email' => $this->email,
							'Name'  => $this->receiverName,
						]
					],
					'Subject'  => $this->subject,
					'TextPart' => "",
					'HTMLPart' => $this->finalMessage,
				]
			]
		];

		$mj->post(Resources::$Email, ['body' => $body]);
	}

	/**
	 * Log email to database
	 *
	 * @param string $status
	 * @param string|null $errorMessage
	 * @return void
	 */
	protected function logEmailToDatabase(string $status, string $errorMessage = null)
	{
		try {
			$emailData = [
				'to_email' => $this->email,
				'to_name' => $this->receiverName ?? null,
				'from_email' => $this->setting->email_from,
				'from_name' => $this->setting->site_name,
				'subject' => $this->subject,
				'body' => $this->finalMessage ?? $this->message,
				'template_name' => $this->templateName ?? null,
				'email_type' => $this->getEmailType(),
				'status' => $status,
				'provider' => $this->setting->mail_config->name ?? 'unknown',
				'provider_response' => $this->response ?? null,
				'error_message' => $errorMessage,
				'user_id' => $this->user->id ?? null,
				'sent_at' => $status === 'sent' ? now() : null,
			];

			EmailLoggingService::logEmail($emailData);
		} catch (Exception $e) {
			// Don't let logging errors break email sending
			\Log::error('Failed to log email to database: ' . $e->getMessage());
		}
	}

	/**
	 * Determine email type based on template or context
	 *
	 * @return string
	 */
	protected function getEmailType(): string
	{
		if ($this->templateName) {
			return match($this->templateName) {
				'WELCOME' => 'welcome',
				'EVER_CODE' => 'verification',
				'PASS_RESET_CODE' => 'password_reset',
				default => 'notification'
			};
		}

		// Check if it's a welcome email by subject
		if (str_contains($this->subject, 'Welcome') || str_contains($this->subject, 'welcome')) {
			return 'welcome';
		}

		// Check if it's verification email
		if (str_contains($this->subject, 'verification') || str_contains($this->subject, 'verify')) {
			return 'verification';
		}

		// Check if it's password reset
		if (str_contains($this->subject, 'password') || str_contains($this->subject, 'reset')) {
			return 'password_reset';
		}

		return 'general';
	}

	/**
	 * Configure some properties
	 *
	 * @return void
	 */
	public function prevConfiguration()
	{
		if ($this->user) {
			$this->email        = $this->user->email;
			$this->receiverName = $this->user->fullname;
		}

		$this->toAddress = $this->email;
	}
}
