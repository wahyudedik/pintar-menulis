<?php

namespace App\Http\Controllers\Operator;

use App\Http\Controllers\Controller;
use App\Models\OperatorProfile;
use Illuminate\Http\Request;

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
            'bio' => 'required|string|min:50|max:500',
            'portfolio_url' => 'nullable|url',
            'specializations' => 'required|array|min:1',
            'specializations.*' => 'string',
            'base_price' => 'required|integer|min:50000',
            'bank_name' => 'nullable|string',
            'bank_account_number' => 'nullable|string',
            'bank_account_name' => 'nullable|string',
            'is_available' => 'boolean',
        ]);

        $profile = auth()->user()->operatorProfile;
        
        if (!$profile) {
            $profile = OperatorProfile::create([
                'user_id' => auth()->id(),
            ]);
        }

        $profile->update($validated);

        return back()->with('success', 'Profile berhasil diupdate!');
    }
}
