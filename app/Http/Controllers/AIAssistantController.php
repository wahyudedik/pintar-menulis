<?php

namespace App\Http\Controllers;

use App\Services\AIAssistantService;
use Illuminate\Http\Request;

class AIAssistantController extends Controller
{
    protected $assistantService;
    
    public function __construct(AIAssistantService $assistantService)
    {
        $this->assistantService = $assistantService;
    }
    
    /**
     * Get AI assistant response
     */
    public function chat(Request $request)
    {
        $validated = $request->validate([
            'message' => 'required|string|max:500',
            'context' => 'nullable|string|in:landing_page,client_generator,client_analytics,general',
        ]);
        
        $response = $this->assistantService->getAssistantResponse(
            $validated['message'],
            $validated['context'] ?? 'general'
        );
        
        return response()->json($response);
    }
    
    /**
     * Get suggested questions
     */
    public function getSuggestions(Request $request)
    {
        $context = $request->input('context', 'general');
        
        $suggestions = $this->assistantService->getSuggestedQuestions($context);
        
        return response()->json([
            'success' => true,
            'suggestions' => $suggestions,
        ]);
    }
    
    /**
     * Get quick tips
     */
    public function getTips(Request $request)
    {
        $context = $request->input('context', 'general');
        
        $tips = $this->assistantService->getQuickTips($context);
        
        return response()->json([
            'success' => true,
            'tips' => $tips,
        ]);
    }
}
