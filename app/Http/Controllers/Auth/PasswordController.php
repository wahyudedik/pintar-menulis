<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class PasswordController extends Controller
{
    /**
     * Update the user's password.
     */
    public function update(Request $request): RedirectResponse
    {
        $user = $request->user();
        
        // Check if user is a Google user setting password for the first time
        $isGoogleUserSettingFirstPassword = $user->google_id && (!$user->password || strlen($user->password) < 10);
        
        if ($isGoogleUserSettingFirstPassword) {
            // For Google users setting first password, skip current password validation
            $validated = $request->validateWithBag('updatePassword', [
                'password' => ['required', Password::defaults(), 'confirmed'],
            ]);
        } else {
            // For regular users, require current password
            $validated = $request->validateWithBag('updatePassword', [
                'current_password' => ['required', 'current_password'],
                'password' => ['required', Password::defaults(), 'confirmed'],
            ]);
        }

        $user->update([
            'password' => Hash::make($validated['password']),
        ]);

        return back()->with('status', 'password-updated');
    }
}
