<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\GeminiService;
use Illuminate\Http\Request;

class AIModelController extends Controller
{
    protected $geminiService;
    
    public function __construct(GeminiService $geminiService)
    {
        $this->geminiService = $geminiService;
    }
    
    /**
     * Show model usage dashboard
     */
    public function index()
    {
        $currentModel = $this->geminiService->getCurrentModel();
        $usageStats = $this->geminiService->getModelUsageStats();
        
        return view('admin.ai-models.index', compact('currentModel', 'usageStats'));
    }
    
    /**
     * Get model usage stats (AJAX)
     */
    public function stats()
    {
        $stats = $this->geminiService->getModelUsageStats();
        $currentModel = $this->geminiService->getCurrentModel();
        
        return response()->json([
            'success' => true,
            'current_model' => $currentModel,
            'stats' => $stats
        ]);
    }
    
    /**
     * Switch to specific model
     */
    public function switchModel(Request $request)
    {
        $request->validate([
            'model' => 'required|string'
        ]);
        
        $success = $this->geminiService->switchModel($request->model);
        
        if ($success) {
            return response()->json([
                'success' => true,
                'message' => 'Model berhasil diganti ke ' . $request->model,
                'current_model' => $this->geminiService->getCurrentModel()
            ]);
        }
        
        return response()->json([
            'success' => false,
            'message' => 'Model tidak ditemukan'
        ], 404);
    }
    
    /**
     * Reset usage stats
     */
    public function resetStats()
    {
        $this->geminiService->resetModelStats();
        
        return response()->json([
            'success' => true,
            'message' => 'Usage stats berhasil direset'
        ]);
    }
}
