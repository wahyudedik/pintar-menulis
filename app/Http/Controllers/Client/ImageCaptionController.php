<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\ImageCaption;
use App\Models\CaptionHistory;
use App\Services\GeminiService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class ImageCaptionController extends Controller
{
    protected $geminiService;

    public function __construct(GeminiService $geminiService)
    {
        $this->geminiService = $geminiService;
    }
    public function index()
    {
        $captions = ImageCaption::where('user_id', auth()->id())
            ->latest()
            ->paginate(12);

        return view('client.image-caption.index', compact('captions'));
    }

    public function create()
    {
        return view('client.image-caption.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg|max:5120', // 5MB max
            'business_type' => 'nullable|string',
            'product_name' => 'nullable|string',
        ]);

        try {
            // Upload image
            $image = $request->file('image');
            $path = $image->store('image-captions', 'public');
            $fullPath = storage_path('app/public/' . $path);

            // Convert image to base64
            $imageData = base64_encode(file_get_contents($fullPath));
            $mimeType = $image->getMimeType();

            // Generate caption using GeminiService with vision
            $result = $this->geminiService->generateImageCaption([
                'user_id' => auth()->id(),
                'image_data' => $imageData,
                'mime_type' => $mimeType,
                'business_type' => $request->business_type ?? 'UMKM',
                'product_name' => $request->product_name ?? '',
            ]);

            // Save to database
            $imageCaption = ImageCaption::create([
                'user_id' => auth()->id(),
                'image_path' => $path,
                'detected_objects' => $result['detected_objects'],
                'caption_single' => $result['caption_single'],
                'caption_carousel' => $result['caption_carousel'],
                'editing_tips' => $result['editing_tips'],
                'dominant_colors' => $result['dominant_colors'],
            ]);

            // Save to caption_history for ML training
            CaptionHistory::create([
                'user_id' => auth()->id(),
                'brief' => "Image Caption: " . ($request->product_name ?? 'Produk'),
                'category' => $request->business_type ?? 'umkm',
                'platform' => 'instagram',
                'tone' => 'engaging',
                'caption' => $result['caption_single'],
                'quality_score' => $result['quality_score'] ?? 0.85,
                'model_used' => $result['model_used'] ?? 'gemini-2.5-flash',
                'generation_time' => $result['generation_time'] ?? 0,
            ]);

            return redirect()->route('image-caption.show', $imageCaption)
                ->with('success', 'Caption berhasil di-generate!');

        } catch (\Exception $e) {
            Log::error('Image Caption Generation Failed', [
                'error' => $e->getMessage(),
                'user_id' => auth()->id()
            ]);
            return back()->with('error', 'Gagal generate caption: ' . $e->getMessage());
        }
    }

    public function show(ImageCaption $imageCaption)
    {
        // Check ownership
        if ($imageCaption->user_id !== auth()->id()) {
            abort(403);
        }

        return view('client.image-caption.show', compact('imageCaption'));
    }

    public function destroy(ImageCaption $imageCaption)
    {
        // Check ownership
        if ($imageCaption->user_id !== auth()->id()) {
            abort(403);
        }

        // Delete image file
        Storage::disk('public')->delete($imageCaption->image_path);

        $imageCaption->delete();

        return redirect()->route('image-caption.index')
            ->with('success', 'Caption berhasil dihapus!');
    }
}
