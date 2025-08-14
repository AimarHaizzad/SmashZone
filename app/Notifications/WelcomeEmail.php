<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\User;

class WelcomeEmail extends Notification implements ShouldQueue
{
    use Queueable;

    public $user;

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
        return (new MailMessage)
            ->subject('Welcome to SmashZone! 🏸')
            ->greeting('Welcome to SmashZone, ' . $notifiable->name . '!')
            ->line('Thank you for joining SmashZone, your premier badminton court booking platform!')
            ->line('**What you can do with SmashZone:**')
            ->line('🏸 **Book Courts:** Reserve badminton courts at your convenience')
            ->line('💳 **Easy Payments:** Secure online payment with multiple options')
            ->line('📱 **Mobile Friendly:** Book from anywhere, anytime')
            ->line('📊 **Track Bookings:** View your booking history and upcoming sessions')
            ->line('🎯 **Special Offers:** Get notified about promotions and discounts')
            ->action('Book Your First Court', url('/courts'))
            ->line('**Getting Started:**')
            ->line('1. Browse available courts and time slots')
            ->line('2. Select your preferred date and time')
            ->line('3. Complete your payment securely')
            ->line('4. Receive confirmation and reminders')
            ->line('**Need Help?**')
            ->line('Our support team is here to help you get the most out of SmashZone.')
            ->line('Contact us anytime for assistance.')
            ->line('We\'re excited to have you as part of the SmashZone community!')
            ->salutation('Happy playing, The SmashZone Team');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'user_id' => $this->user->id,
            'user_name' => $this->user->name,
            'user_email' => $this->user->email,
            'registration_date' => $this->user->created_at,
        ];
    }
}
