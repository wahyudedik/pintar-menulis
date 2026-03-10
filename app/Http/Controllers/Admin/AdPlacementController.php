<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdPlacement;
use Illuminate\Http\Request;

class AdPlacementController extends Controller
{
    /**
     * Display all ad placements
     */
    public function index()
    {
        $placements = AdPlacement::all();
        
        // Ensure all locations exist
        $locations = [
            'article_list_top',
            'article_list_bottom',
            'article_detail_top',
            'article_detail_middle',
            'article_detail_bottom',
        ];

        foreach ($locations as $location) {
            if (!$placements->where('location', $location)->first()) {
                AdPlacement::create([
                    'location' => $location,
                    'ad_code' => null,
                    'is_active' => false,
                ]);
            }
        }

        $placements = AdPlacement::all();
        return view('admin.ad-placements.index', compact('placements'));
    }

    /**
     * Update ad placement
     */
    public function update(Request $request, AdPlacement $placement)
    {
        $validated = $request->validate([
            'ad_code' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        $placement->update($validated);

        return redirect()->route('admin.ad-placements.index')
            ->with('success', 'Ad placement updated successfully');
    }

    /**
     * Toggle ad placement active status
     */
    public function toggle(AdPlacement $placement)
    {
        $placement->update(['is_active' => !$placement->is_active]);

        return redirect()->route('admin.ad-placements.index')
            ->with('success', 'Ad placement status updated');
    }
}
