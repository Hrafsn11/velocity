<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class WelcomeUserNotification extends Notification
{
    use Queueable;

    public $password;
    public $isEmployee;

    /**
     * Create a new notification instance.
     */
    public function __construct(string $password, bool $isEmployee = false)
    {
        $this->password = $password;
        $this->isEmployee = $isEmployee;
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
        $userType = $this->isEmployee ? 'Team Member' : 'User';
        
        return (new MailMessage)
            ->subject('Welcome to Velocity Project Management')
            ->greeting("Hello {$notifiable->name}!")
            ->line("Welcome to Velocity Project Management System. Your account has been created as a {$userType}.")
            ->line('Here are your login credentials:')
            ->line("**Email:** {$notifiable->email}")
            ->line("**Temporary Password:** {$this->password}")
            ->action('Login to Your Account', url('/login'))
            ->line('⚠️ **Important:** For security reasons, please remember your password')
            ->line('If you have any questions, please contact administrator.')
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
            'password' => $this->password,
            'is_employee' => $this->isEmployee,
        ];
    }
}
