<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\BrandVoice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BrandVoiceController extends Controller
{
    /**
     * Get all brand voices for current user
     */
    public function index()
    {
        $brandVoices = Auth::user()->brandVoices()->latest()->get();
        
        return response()->json([
            'success' => true,
            'brand_voices' => $brandVoices
        ]);
    }

    /**
     * Store a new brand voice
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'industry' => 'nullable|string',
            'target_market' => 'nullable|string',
            'tone' => 'required|string',
            'platform' => 'required|string',
            'keywords' => 'nullable|string',
            'local_language' => 'nullable|string',
            'brand_description' => 'nullable|string',
            'is_default' => 'nullable|boolean',
        ]);

        $brandVoice = Auth::user()->brandVoices()->create($validated);

        // If set as default, unset others
        if ($validated['is_default'] ?? false) {
            $brandVoice->setAsDefault();
        }

        return response()->json([
            'success' => true,
            'brand_voice' => $brandVoice,
            'message' => 'Brand voice saved successfully'
        ]);
    }

    /**
     * Delete a brand voice
     */
    public function destroy(BrandVoice $brandVoice)
    {
        // Ensure user owns this brand voice
        if ($brandVoice->user_id !== Auth::id()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 403);
        }

        $brandVoice->delete();

        return response()->json([
            'success' => true,
            'message' => 'Brand voice deleted successfully'
        ]);
    }
}
