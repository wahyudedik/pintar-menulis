<?php

namespace App\Services;

class AIService
{
    protected $geminiService;

    public function __construct(GeminiService $geminiService)
    {
        $this->geminiService = $geminiService;
    }

    public function generateCopywriting(array $params)
    {
        return $this->geminiService->generateCopywriting($params);
    }
}
