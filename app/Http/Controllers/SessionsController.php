<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Organization;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;


class SessionsController extends Controller
{
    public function create()
    {
        return view('session.login-session');
    }

    // global login
    public function store(Request $request)
    {
        $attributes = $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        if (Auth::attempt(array_merge($attributes, ['is_active' => 1]))) {
            $user = Auth::user();

            if ($user->role !== 'superadmin') {
                Auth::logout();

                return back()->withErrors([
                    'email' => 'You are not authorized to access this area.'
                ]);
            }

            $request->session()->regenerate();
            session([
                'login_type' => 'superadmin'
            ]);

            $user->login_at = now();
            $user->save();
            return redirect()->route('superadmin.dashboard')->with([
                'success' => 'You are logged in as Super Admin.'
            ]);
        }

        return back()->withErrors([
            'email' => 'Email or password invalid or account is inactive.'
        ]);
    }


    public function tenantLinkLogin($token)
    {
        $organization = Organization::where('token', $token)->firstOrFail();

        app()->instance('currentOrganization', $organization);

        return view('session.login-session-tenant', [
            'organization' => $organization,
            'token' => $token,
        ]);
    }



    public function tenantLogin(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
            'token' => 'required',
        ]);

        $organization = Organization::where('token', $request->token)->first();

        if (!$organization) {
            return back()->withErrors(['email' => 'Organization token invalid.']);
        }

        if (Auth::attempt([
            'email' => $request->email,
            'password' => $request->password,
            'is_active' => 1
        ])) {
            
            $user = Auth::user();

            if ($user->organization_id !== $organization->id) {
                
                Auth::logout(); 
                return back()->withErrors([
                    'email' => 'You are not registered in ' . $organization->name . ' sector.'
                ]);
            }

            $request->session()->regenerate();
            
            session([
                'login_type' => 'tenant',
                'organization_id' => $user->organization_id,
                'login_token' => $request->token 
            ]);

            $user->login_at = now();
            $user->save();
            
            if($user->role == 'admin'){
                return redirect()->route('org.dashboard-admin');
            } else {
                return redirect()->route('org.dashboard-user');
            }
        }

        return back()->withErrors(['email' => 'Invalid credentials or account is inactive']);
    }

    public function destroy(Request $request)
    {
        $loginType = session('login_type');
        $loginToken = session('login_token');

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        if ($loginType === 'tenant') {
            return redirect('organization/login/' . $loginToken)
                ->with('success', 'You have been logged out.');
        }

        return redirect()
            ->route('login-superadmin-index')
            ->with('success', 'You have been logged out.');
    }

}
