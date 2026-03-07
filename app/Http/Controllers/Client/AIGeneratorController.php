<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Services\AIService;
use Illuminate\Http\Request;

class AIGeneratorController extends Controller
{
    protected $aiService;

    public function __construct(AIService $aiService)
    {
        $this->aiService = $aiService;
    }

    public function index()
    {
        return view('client.ai-generator');
    }

    /**
     * Check if user is first-time (has no caption history)
     */
    public function checkFirstTime()
    {
        $userId = auth()->id();
        $historyCount = \App\Models\CaptionHistory::where('user_id', $userId)->count();

        return response()->json([
            'success' => true,
            'is_first_time' => ($historyCount === 0)
        ]);
    }


    public function generate(Request $request)
    {
        try {
            $validated = $request->validate([
                'category' => 'required|string',
                'subcategory' => 'required|string',
                'platform' => 'nullable|string',
                'brief' => 'required|string|min:10',
                'tone' => 'required|string',
                'keywords' => 'nullable|string',
                'generate_variations' => 'nullable|boolean',
                'variation_count' => 'nullable|integer|in:5,10,15,20',
                'auto_hashtag' => 'nullable|boolean',
                'local_language' => 'nullable|string',
                'mode' => 'nullable|string|in:simple,advanced',
            ]);

            $params = [
                'category' => $validated['category'],
                'subcategory' => $validated['subcategory'],
                'brief' => $validated['brief'],
                'tone' => $validated['tone'],
                'platform' => $validated['platform'] ?? 'instagram',
                'keywords' => $validated['keywords'] ?? '',
                'generate_variations' => $validated['generate_variations'] ?? false,
                'variation_count' => $validated['variation_count'] ?? 5,
                'auto_hashtag' => $validated['auto_hashtag'] ?? true,
                'local_language' => $validated['local_language'] ?? '',
                'mode' => $validated['mode'] ?? 'simple',
                'user_id' => auth()->id(), // Add user_id for history tracking
            ];

            $result = $this->aiService->generateCopywriting($params);

            // Record caption history for each generated caption
            $lastCaptionId = null;
            if (isset($result) && is_string($result)) {
                // Parse result to extract individual captions
                $captions = $this->extractCaptions($result);
                
                foreach ($captions as $caption) {
                    $history = \App\Models\CaptionHistory::recordCaption(
                        auth()->id(),
                        $caption,
                        $params
                    );
                    // Store the last caption ID for rating
                    $lastCaptionId = $history->id;
                }
            }

            return response()->json([
                'success' => true,
                'result' => $result,
                'caption_id' => $lastCaptionId // Return caption ID for rating
            ]);
            
        } catch (\Illuminate\Validation\ValidationException $e) {
            $errors = [];
            foreach ($e->errors() as $field => $messages) {
                $errors[] = implode(', ', $messages);
            }
            
            return response()->json([
                'success' => false,
                'message' => 'Validation error: ' . implode('; ', $errors)
            ], 422);
            
        } catch (\Exception $e) {
            \Log::error('AI Generate Error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Extract individual captions from AI result
     */
    private function extractCaptions(string $result): array
    {
        $captions = [];
        
        // Try to split by numbered list (1., 2., 3., etc)
        if (preg_match_all('/\d+\.\s*(.+?)(?=\d+\.|$)/s', $result, $matches)) {
            $captions = array_map('trim', $matches[1]);
        } else {
            // If no numbered list, treat whole result as one caption
            $captions = [$result];
        }
        
        return array_filter($captions); // Remove empty entries
    }
}

