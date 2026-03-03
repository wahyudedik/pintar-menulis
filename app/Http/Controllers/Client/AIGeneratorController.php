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
            ]);

            $result = $this->aiService->generateCopywriting([
                'type' => $validated['subcategory'],
                'brief' => $validated['brief'],
                'tone' => $validated['tone'],
                'platform' => $validated['platform'] ?? 'instagram',
                'keywords' => $validated['keywords'] ?? '',
            ]);

            return response()->json([
                'success' => true,
                'result' => $result
            ]);
            
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Data tidak valid: ' . implode(', ', $e->errors())
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

