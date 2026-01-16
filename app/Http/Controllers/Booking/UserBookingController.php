<?php

namespace App\Http\Controllers\Booking;

use App\Models\Booking;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class UserBookingController extends Controller
{
    public function show_booking_history(){
        $bookings = Booking::with(['user', 'item'])
            ->where('user_id', auth()->user()->id)
            ->where('organization_id', auth()->user()->organization_id)
            ->whereIn('status', ['approved', 'rejected', 'pending'])
            ->latest()
            ->get();

        return view('booking.booking-history', compact('bookings'));
    }
}
