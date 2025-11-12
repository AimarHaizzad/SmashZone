<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\User;

class StaffAccountCreated extends Notification implements ShouldQueue
{
    use Queueable;

    public $staff;
    public $temporaryPassword;

    /**
     * Create a new notification instance.
     */
    public function __construct(User $staff, string $temporaryPassword)
    {
        $this->staff = $staff;
        $this->temporaryPassword = $temporaryPassword;
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
            ->subject('Your SmashZone Staff Account Has Been Created! 🏸')
            ->greeting('Welcome to the SmashZone Team, ' . $this->staff->name . '!')
            ->line('Your staff account has been successfully created by the owner.')
            ->line('**Your Account Details:**')
            ->line('📧 **Email:** ' . $this->staff->email)
            ->line('🔑 **Temporary Password:** ' . $this->temporaryPassword)
            ->line('👤 **Role:** Staff Member')
            ->line('📱 **Position:** ' . ($this->staff->position ?? 'Not specified'))
            ->line('')
            ->line('**What you can do as a Staff Member:**')
            ->line('🏸 **Manage Courts:** View and manage court availability')
            ->line('📅 **Manage Bookings:** Track customer bookings')
            ->line('👥 **Customer Service:** Help customers with their bookings')
            ->line('📊 **View Reports:** Access booking and court statistics')
            ->line('')
            ->line('**Important Security Notes:**')
            ->line('🔐 **Change Your Password:** Please change your password after first login')
            ->line('🛡️ **Keep Credentials Safe:** Do not share your login details')
            ->line('📱 **Secure Access:** Always log out from shared computers')
            ->line('')
            ->line('**Getting Started:**')
            ->line('1. Visit the SmashZone website')
            ->line('2. Click "Login" and use your credentials above')
            ->line('3. Change your password immediately')
            ->line('4. Explore the staff dashboard and features')
            ->action('Login to SmashZone', url('/login'))
            ->line('**Need Help?**')
            ->line('If you have any questions or need assistance, please contact the owner or our support team.')
            ->line('We\'re excited to have you as part of the SmashZone team!')
            ->salutation('Best regards, The SmashZone Team');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'staff_id' => $this->staff->id,
            'staff_name' => $this->staff->name,
            'staff_email' => $this->staff->email,
            'staff_position' => $this->staff->position,
            'account_created_at' => $this->staff->created_at,
            'created_by' => auth()->user()->name ?? 'System',
        ];
    }
}
