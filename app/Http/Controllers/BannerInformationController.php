<?php

namespace App\Http\Controllers;

use App\Models\BannerInformation;
use Illuminate\Http\Request;

class BannerInformationController extends Controller
{
    /**
     * Get active banner by type (API endpoint)
     */
    public function getByType(Request $request, string $type)
    {
        $banner = BannerInformation::getActiveByType($type);
        
        if (!$banner) {
            return response()->json([
                'success' => false,
                'message' => 'No active banner found'
            ]);
        }

        return response()->json([
            'success' => true,
            'banner' => [
                'id' => $banner->id,
                'title' => $banner->title,
                'content' => $banner->content,
            ]
        ]);
    }
}
