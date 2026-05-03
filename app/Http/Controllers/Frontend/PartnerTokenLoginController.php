<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use App\Models\User;

class PartnerTokenLoginController extends Controller
{
    public function login(string $token)
    {
        $userId = Cache::pull('partner_login_token_' . $token);

        if (!$userId) {
            abort(403, 'Invalid or expired token.');
        }

        $user = User::findOrFail($userId);
        Auth::login($user);

        return redirect()->route('partner.dashboard');
    }
}
