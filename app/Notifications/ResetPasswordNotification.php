<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ResetPasswordNotification extends Notification
{
    use Queueable;

    public $temporaryPassword;

    /**
     * Create a new notification instance.
     */
    public function __construct(string $temporaryPassword)
    {
        $this->temporaryPassword = $temporaryPassword;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<string, mixed>
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
        return (new MailMessage)
            ->subject('Password Reset - Velocity Project Management')
            ->greeting("Hello {$notifiable->name}!")
            ->line('Your password has been reset by the administrator.')
            ->line('Here is your new temporary password:')
            ->line("**Temporary Password:** {$this->temporaryPassword}")
            ->action('Login Now', url('/login'))
            ->line('⚠️ **Important:** For security reasons, please remember your password')
            ->line('If you did not request this password reset, please contact administrator immediately.')
            ->salutation('Best regards, Velocity Team');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'temporary_password' => $this->temporaryPassword,
        ];
    }
}
