<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BannerInformation;
use Illuminate\Http\Request;

class BannerInformationController extends Controller
{
    /**
     * Display banner management page
     */
    public function index()
    {
        $banners = BannerInformation::orderBy('type')->get();
        
        // Ensure all types exist
        $types = ['landing', 'client', 'operator', 'guru'];
        foreach ($types as $type) {
            if (!$banners->where('type', $type)->first()) {
                BannerInformation::create([
                    'type' => $type,
                    'title' => null,
                    'content' => null,
                    'is_active' => false,
                ]);
            }
        }
        
        $banners = BannerInformation::orderBy('type')->get();
        
        return view('admin.banner-information.index', compact('banners'));
    }

    /**
     * Update banner
     */
    public function update(Request $request, BannerInformation $banner)
    {
        $validated = $request->validate([
            'title' => 'nullable|string|max:255',
            'content' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        // If title or content is empty, set is_active to false
        if (empty($validated['title']) || empty($validated['content'])) {
            $validated['is_active'] = false;
        }

        $banner->update($validated);

        return redirect()->route('admin.banner-information.index')
            ->with('success', 'Banner updated successfully');
    }
}
