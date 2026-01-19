<?php

namespace App\Notifications;

use App\Models\Booking;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class BookingStatusUpdatedNotification extends Notification implements ShouldQueue
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
        $status = ucfirst($this->booking->status);
        $color = $this->booking->status == 'approved' ? 'success' : 'error';

        $mailMessage = (new MailMessage)
                    ->subject('Booking Update: ' . $status)
                    ->greeting('Hello ' . $notifiable->name . ',')
                    ->line('Your booking status has been updated.')
                    ->line('Item: ' . $this->booking->item->name)
                    ->line('Date: ' . $this->booking->booking_date)
                    ->line('Status: ' . $status);

        if ($this->booking->status == 'rejected' && $this->booking->reject_reason) {
            $mailMessage->line('Reason: ' . $this->booking->reject_reason);
        }

        return $mailMessage->line('Thank you for using our facility.');
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
