<?php

namespace App\Http\Controllers\Operator;

use App\Http\Controllers\Controller;
use App\Models\OperatorProfile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    public function edit()
    {
        $profile = auth()->user()->operatorProfile;
        
        if (!$profile) {
            $profile = OperatorProfile::create([
                'user_id' => auth()->id(),
                'bio' => '',
                'specializations' => [],
                'base_price' => 50000,
                'is_available' => true,
            ]);
        }

        return view('operator.profile-edit', compact('profile'));
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'name'                => 'required|string|max:255',
            'phone'               => 'nullable|string|max:20',
            'location'            => 'nullable|string|max:100',
            'avatar'              => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'bio'                 => 'required|string|min:50|max:500',
            'portfolio_url'       => 'nullable|url',
            'specializations'     => 'required|array|min:1',
            'specializations.*'   => 'string',
            'base_price'          => 'required|integer|min:50000',
            'bank_name'           => 'nullable|string',
            'bank_account_number' => 'nullable|string',
            'bank_account_name'   => 'nullable|string',
            'is_available'        => 'boolean',
        ]);

        $user = auth()->user();

        // Handle avatar upload
        if ($request->hasFile('avatar')) {
            if ($user->avatar && !str_starts_with($user->avatar, 'http')) {
                Storage::disk('public')->delete($user->avatar);
            }
            $user->avatar = $request->file('avatar')->store('avatars', 'public');
        }

        $user->name     = $validated['name'];
        $user->phone    = $validated['phone'] ?? $user->phone;
        $user->location = $validated['location'] ?? $user->location;
        $user->save();

        $profile = $user->operatorProfile;
        if (!$profile) {
            $profile = OperatorProfile::create(['user_id' => $user->id]);
        }

        $profile->update([
            'bio'                 => $validated['bio'],
            'portfolio_url'       => $validated['portfolio_url'] ?? $profile->portfolio_url,
            'specializations'     => $validated['specializations'],
            'base_price'          => $validated['base_price'],
            'bank_name'           => $validated['bank_name'] ?? $profile->bank_name,
            'bank_account_number' => $validated['bank_account_number'] ?? $profile->bank_account_number,
            'bank_account_name'   => $validated['bank_account_name'] ?? $profile->bank_account_name,
            'is_available'        => $request->boolean('is_available'),
        ]);

        return back()->with('success', 'Profile berhasil diupdate!');
    }
}
