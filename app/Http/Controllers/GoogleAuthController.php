<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Str;
use App\Models\Organization;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Laravel\Socialite\Facades\Socialite;

class GoogleAuthController extends Controller
{
    public function redirectToGoogleSuperadmin()
    {
        return Socialite::driver('google')->redirect();
    }

    public function handleGoogleCallbackSuperadmin(){
        try {
            $googleUser = Socialite::driver('google')->user();
            $user = User::where('google_id', $googleUser->id)
                ->orWhere('email', $googleUser->email)
                ->first();
            
                if($user){
                    if(!$user->google_id){
                        $user->update([
                            'google_id'=>$googleUser->id
                        ]);
                    }

                    if($user->role !== 'superadmin'){
                        return redirect()->route('login-superadmin-index')
                            ->withErrors(['email' => 'Access denied. You are not a Superadmin.']);
                    }

                    Auth::login($user);
                    session([
                        'login_type'=>'superadmin'
                    ]);
                    $user->update([
                        'login_at'=>now()
                    ]);
                    return redirect()->route('superadmin.dashboard');

                } else {
                    return redirect()->route('login-superadmin-index')->withErrors(['email' => 'Account not found. Please register first.']);
                }
            
        } catch(\Exception $e){
            return redirect()->route('login-superadmin-index')->withErrors(['email' => 'Google Login failed.']);
        }
    }

    public function redirectToGoogleTenant($token)
    {
        $organization = Organization::where('token', $token)->firstOrFail();
        session(['target_org_token' => $token]);

        return Socialite::driver('google')
            ->redirectUrl(route('organization.google.callback'))
            ->redirect();
    }

    public function handleGoogleCallbackTenant()
    {
        try {
            $orgToken = session('target_org_token');
            if (!$orgToken) {
                return redirect('/')->with('error', 'Session expired. Please try again from the organization link.');
            }

            $organization = Organization::where('token', $orgToken)->firstOrFail();
            
            $googleUser = Socialite::driver('google')
                ->redirectUrl(route('organization.google.callback'))
                ->user();

            $user = User::where('google_id', $googleUser->id)
                        ->orWhere('email', $googleUser->email)
                        ->first();

            if (!$user) {
                $user = User::create([
                    'name' => $googleUser->name,
                    'email' => $googleUser->email,
                    'google_id' => $googleUser->id,
                    'password' => Hash::make(Str::random(16)), 
                    'phone' => '-', 
                    'role' => 'user', 
                    'organization_id' => $organization->id,
                    'is_active' => 1
                ]);
            } else {
                $user->update(['google_id' => $googleUser->id]);

                if ($user->organization_id != $organization->id) {
                     return redirect()->route('organization.login.link', $orgToken)
                        ->withErrors(['email' => 'This email is registered to another organization.']);
                }
            }

            Auth::login($user);
            session()->regenerate();

            session([
                'login_type' => 'tenant',
                'organization_id' => $user->organization_id,
                'login_token' => $orgToken
            ]);

            $user->update(['login_at' => now()]);

            if ($user->role == 'admin') {
                return redirect()->route('org.dashboard-admin');
            } else {
                return redirect()->route('org.dashboard-user');
            }

        } catch (\Exception $e) {
            $orgToken = session('target_org_token');
            return redirect()->route('organization.login.link', $orgToken ?? '')
                ->withErrors(['email' => 'Google Login failed: ' . $e->getMessage()]);
        }
    }
}
