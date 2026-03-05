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
                'auto_hashtag' => 'nullable|boolean',
                'local_language' => 'nullable|string',
            ]);

            $result = $this->aiService->generateCopywriting([
                'category' => $validated['category'],
                'subcategory' => $validated['subcategory'],
                'brief' => $validated['brief'],
                'tone' => $validated['tone'],
                'platform' => $validated['platform'] ?? 'instagram',
                'keywords' => $validated['keywords'] ?? '',
                'generate_variations' => $validated['generate_variations'] ?? false,
                'auto_hashtag' => $validated['auto_hashtag'] ?? true,
                'local_language' => $validated['local_language'] ?? '',
            ]);

            return response()->json([
                'success' => true,
                'result' => $result
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
}

