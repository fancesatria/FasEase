<?php

namespace App\Notifications;

use App\Models\Booking;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Contracts\Queue\ShouldQueue; // biar ga loading lama

class BookingNotification extends Notification implements ShouldQueue
{
    use Queueable;
    public $booking;

    /**
     * Create a new notification instance.
     */
    public function __construct(Booking $booking)
    {
        $this->booking = $booking;
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
                    ->subject('New Booking Request - FasEase')
                    ->greeting('Hello, Admin - '.$this->booking->user->name)
                    ->line('You have a new booking request.')
                    ->line('Item: ' . $this->booking->item->name) 
                    ->line('User: ' . $this->booking->user->name) 
                    ->line('Date: ' . $this->booking->booking_date)
                    ->line('Time: ' . $this->booking->start_time . ' - ' . $this->booking->end_time)
                    ->action('Review Booking', url('organization/booking-management'))
                    ->line('Please review this request.');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }
}
