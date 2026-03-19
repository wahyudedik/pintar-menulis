<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class ProfileController extends Controller
{
    public function edit(Request $request): View
    {
        $user = $request->user()->load('operatorProfile');
        return view('profile.edit', compact('user'));
    }

    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $user = $request->user();
        $data = $request->validated();

        // Handle avatar upload
        if ($request->hasFile('avatar')) {
            // Delete old avatar if it's a stored file (not a Google URL)
            if ($user->avatar && !str_starts_with($user->avatar, 'http')) {
                Storage::disk('public')->delete($user->avatar);
            }
            $data['avatar'] = $request->file('avatar')->store('avatars', 'public');
        } else {
            unset($data['avatar']); // don't overwrite with null
        }

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        $user->fill($data)->save();

        // Update operator profile fields if operator
        if ($user->role === 'operator' && $request->has('operator')) {
            $op = $request->input('operator', []);
            $profile = $user->operatorProfile;
            if ($profile) {
                $profile->update([
                    'bio'                 => $op['bio'] ?? $profile->bio,
                    'portfolio_url'       => $op['portfolio_url'] ?? $profile->portfolio_url,
                    'specializations'     => isset($op['specializations'])
                        ? array_filter(explode(',', $op['specializations']))
                        : $profile->specializations,
                    'base_price'          => $op['base_price'] ?? $profile->base_price,
                    'bank_name'           => $op['bank_name'] ?? $profile->bank_name,
                    'bank_account_number' => $op['bank_account_number'] ?? $profile->bank_account_number,
                    'bank_account_name'   => $op['bank_account_name'] ?? $profile->bank_account_name,
                    'is_available'        => isset($op['is_available']),
                ]);
            }
        }

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();
        Auth::logout();
        $user->delete();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }

    public function disconnectGoogle(Request $request): RedirectResponse
    {
        $user = $request->user();

        if (!$user->password || strlen($user->password) < 10) {
            return Redirect::route('profile.edit')
                ->with('error', 'Set password terlebih dahulu sebelum memutus akun Google.');
        }

        $user->update(['google_id' => null, 'provider' => 'email']);

        return Redirect::route('profile.edit')->with('status', 'google-disconnected');
    }
}
