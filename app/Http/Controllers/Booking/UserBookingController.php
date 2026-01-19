<?php

namespace App\Http\Controllers\Booking;

use App\Models\User;
use App\Models\Booking;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Notifications\BookingNotification;
use Illuminate\Support\Facades\Notification;

class UserBookingController extends Controller
{
    public function show_booking_history(){
        $bookings = Booking::with(['user', 'item'])
            ->where('user_id', auth()->user()->id)
            ->where('organization_id', auth()->user()->organization_id)
            ->whereIn('status', ['approved', 'rejected', 'pending', 'completed', 'canceled'])
            ->latest()
            ->get();

        return view('booking.booking-history', compact('bookings'));
    }

    public function show_user_current_booking(){
        $bookings = Booking::with(['user', 'item'])
            ->where('user_id', auth()->user()->id)
            ->where('organization_id', auth()->user()->organization_id)
            ->where('status', ['pending', 'approved'])
            ->latest()
            ->get();

        return view('booking.my-booking-user', compact('bookings'));
    }

    public function checkAvailability(Request $request)
    {
        $itemId = $request->item_id;
        $date = $request->date;

        // Ambil yang statusnya bukan rejected/cancelled pada tanggal & item tersebut
        $bookedSlots = Booking::where('item_id', $itemId)
            ->where('booking_date', $date)
            ->where('status', '!=', 'rejected') 
            ->pluck('start_time')
            ->toArray();

        return response()->json($bookedSlots);
    }

    public function store(Request $request)
    {
        $request->validate([
            'item_id' => 'required',
            'booking_date' => 'required|date',
            'start_time' => 'required',
            'end_time' => 'required',
        ]);

        $user = Auth::user();

        $booking = Booking::create([
            'user_id' => $user->id,
            'organization_id' => $user->organization_id ?? null, // Asumsi ada relasi ini
            'item_id' => $request->item_id,
            'booking_date' => $request->booking_date,
            'start_time' => $request->start_time,
            'end_time' => $request->end_time,
            'status' => 'pending', // Default status
        ]);

        $admins = User::where('organization_id', $booking->organization->id)
            ->where('role', 'admin')
            ->get();

        Notification::send($admins, new BookingNotification($booking));
        return redirect()->back()->with('success', 'Booking created successfully! please wait for approval');
    }

    public function cancel(Request $request, $id){
        $request->validate([
            'cancel_reason' => 'required|string|max:255'
        ]);

        Booking::findOrFail($id)->update([
            'status' => 'canceled',
            'cancel_reason' => $request->cancel_reason
        ]);

        return back()->with('success', 'Booking canceled');
    }
}
