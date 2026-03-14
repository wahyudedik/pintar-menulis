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

    public function generateImageCaption(array $params)
    {
        return $this->geminiService->generateImageCaption($params);
    }

    public function generateText(string $prompt)
    {
        return $this->geminiService->generateText($prompt);
    }
}
