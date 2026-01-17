<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Item;
use App\Models\User;
use App\Models\Booking;
use App\Models\Category;
use App\Models\Organization;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function home()
    {
        $totalUsers = User::count();
        $totalOrganizations = Organization::count();
        $activeUserToday = User::whereDate('login_at', Carbon::today())->count();
        $blockedUsers = User::where('is_active', 0)->count();
        $recentUsers = User::orderBy('created_at', 'desc')->take(10)->get();
        $recentOrganizations = Organization::orderBy('created_at', 'desc')->take(10)->get();

        // User per bulan
        $usersPerMonth = User::selectRaw('MONTH(created_at) as month, COUNT(*) as count')
            ->whereYear('created_at', date('Y'))
            ->groupBy('month')
            ->pluck('count', 'month')
            ->toArray();

        $userCounts = [];
        for ($i = 1; $i <= 12; $i++) {
            $userCounts[] = $usersPerMonth[$i] ?? 0;
        }

        return view('dashboard', compact(
            'totalUsers',
            'totalOrganizations',
            'activeUserToday',
            'blockedUsers',
            'recentUsers',
            'recentOrganizations',
            'userCounts'
        ));
    }

    public function home_tenant()
    {
        $user = auth()->user();

        if (!$user || !$user->organization) {
            abort(403, 'Organization not found.');
        }

        $orgId = $user->organization_id;
        $pendingBookings = Booking::where('organization_id', $orgId)->where('status', 'pending')->count();
        $activeBookings = Booking::where('organization_id', $orgId)
            ->where('status','approved')
            ->where('start_time', '<=', now())
            ->where('end_time', '>=', now())
            ->count();

        $totalItems = Item::where('organization_id', $orgId)->count();
        $totalCategories = Category::where('organization_id', $orgId)->count();

        $bookingsPerMonth = Booking::selectRaw('MONTH(created_at) as month, COUNT(*) as count')
            ->where('organization_id', $orgId)
            ->whereYear('created_at', date('Y'))
            ->groupBy('month')
            ->pluck('count', 'month')
            ->toArray();

        $bookingChart = [];
        for ($i = 1; $i <= 12; $i++) {
            $bookingChart[] = $bookingsPerMonth[$i] ?? 0;
        }

        $recentRequests = Booking::with(['user', 'item']) 
                            ->where('organization_id', $orgId)
                            ->where('status', 'pending')
                            ->latest()
                            ->take(10)
                            ->get();

        return view('dashboard-admin', [
            'organization' => $user->organization,
            'pendingBookings' => $pendingBookings,
            'activeBookings' => $activeBookings,
            'totalItems' => $totalItems,
            'totalCategories' => $totalCategories,
            'bookingChart' => $bookingChart,
            'recentRequests' => $recentRequests,
        ]);
    }

    public function home_tenant_user(){
        $user = auth()->user();

        if (!$user || !$user->organization) {
            abort(403, 'Organization not found.');
        }

        $orgId = $user->organization_id;
        $totalBookings = Booking::where('organization_id', $orgId)
            ->where('user_id', $user->id)
            ->count();
        $pendingBookings = Booking::where('organization_id', $orgId)
            ->where('user_id', $user->id)
            ->where('status', 'pending')->count();

        $approvedBookings = Booking::where('organization_id', $orgId)
            ->where('user_id', $user->id)
            ->where('status', 'approved')->count();
        
        $rejectedBookings = Booking::where('organization_id', $orgId)
            ->where('user_id', $user->id)
            ->where('status', 'rejected')->count();
        $categories = Category::where('organization_id', $orgId)->get();
        
        return view('dashboard-user', compact(
            'categories',
            'totalBookings',
            'pendingBookings',
            'approvedBookings',
            'rejectedBookings'
        ));
    }
}
