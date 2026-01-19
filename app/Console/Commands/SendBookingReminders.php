<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use App\Models\Booking;
use Illuminate\Console\Command;
use App\Notifications\BookingReminderNotification;

class SendBookingReminders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:send-booking-reminders';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send reminder 10 minutes before booking ends';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $targetTime = Carbon::now()->addMinutes(10);

        $startWindow = $targetTime->copy()->startOfMinute();
        $endWindow = $targetTime->copy()->endOfMinute();

        // cari kriteria booking
        $bookings = Booking::where('status', 'approved')
            ->where('reminder_sent', false)
            ->whereBetween('end_time', ['$startWindow, $endWindow'])
            ->get();
        
        foreach ($bookings as $booking){
            if($booking->user){
                $booking->user->notify(new BookingReminderNotification($booking));
                $booking->update(['reminder_sent' => true]);
                $this->info("Reminder sent to booking ID: {$booking->id}");
            }
        }
    }
}
