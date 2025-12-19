<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\User;

class WelcomeNotification extends Notification
{
    use Queueable;

    protected $user;

    /**
     * Create a new notification instance.
     */
    public function __construct(User $user)
    {
        $this->user = $user;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $mailMessage = (new MailMessage)
            ->subject('🎉 Welcome to ApnaCrowdfunding - Your Journey Begins!')
            ->greeting('Hello ' . $this->user->firstname . ' ' . $this->user->lastname . '!')
            ->line('🌟 Welcome to ApnaCrowdfunding! We\'re thrilled to have you join our community of entrepreneurs and innovators.')
            ->line('Your account has been created successfully and you\'re ready to start your fundraising journey!')
            ->line('')
            ->line('📋 **Your Account Details:**')
            ->line('👤 Username: ' . $this->user->username)
            ->line('📧 Email: ' . $this->user->email)
            ->line('📱 Mobile: ' . ($this->user->mobile ?? 'Not provided'));

        // Add business information if available
        if (!empty($this->user->business_name)) {
            $mailMessage->line('')
                ->line('🏢 **Business Information:**')
                ->line('Business Name: ' . $this->user->business_name)
                ->line('Business Type: ' . ($this->user->business_type ?? 'Not specified'))
                ->line('Industry: ' . ($this->user->industry ?? 'Not specified'));
        }

        $mailMessage->line('')
            ->line('🚀 **What\'s Next?**')
            ->line('• Create your first fundraising campaign')
            ->line('• Set up your business profile')
            ->line('• Connect with potential investors')
            ->line('• Start raising funds for your dreams!')
            ->line('')
            ->line('💡 **Pro Tips:**')
            ->line('• Complete your profile to build trust')
            ->line('• Use high-quality images for your campaigns')
            ->line('• Share your story authentically')
            ->line('• Engage with your supporters regularly')
            ->action('🚀 Start Your First Campaign', url('/login'))
            ->line('')
            ->line('Need help? Our support team is here for you!')
            ->line('📞 Contact us anytime - we\'re here to help you succeed!')
            ->line('')
            ->line('Best regards,')
            ->line('The ApnaCrowdfunding Team 💙');

        return $mailMessage;
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'message' => 'Welcome to ApnaCrowdfunding! Your account has been created successfully.',
            'user_id' => $this->user->id,
        ];
    }
}
