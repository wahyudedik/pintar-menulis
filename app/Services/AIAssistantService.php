<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * AI Assistant Service
 * 
 * Provides AI-powered assistance for:
 * - Application usage guidance
 * - Digital marketing tips
 * - Caption optimization advice
 * - Best practices
 */
class AIAssistantService
{
    private $geminiService;
    
    public function __construct(GeminiService $geminiService)
    {
        $this->geminiService = $geminiService;
    }
    
    /**
     * Get AI assistant response
     */
    public function getAssistantResponse(string $userMessage, string $context = 'general'): array
    {
        try {
            $systemPrompt = $this->getSystemPrompt($context);
            
            $response = $this->geminiService->generateText(
                prompt: $systemPrompt . "\n\nUser: " . $userMessage,
                maxTokens: 500,
                temperature: 0.7
            );
            
            return [
                'success' => true,
                'response' => $response,
                'context' => $context,
            ];
        } catch (\Exception $e) {
            Log::error('AI Assistant Error: ' . $e->getMessage());
            
            return [
                'success' => false,
                'response' => 'Maaf, asisten sedang tidak tersedia. Silakan coba lagi.',
                'error' => $e->getMessage(),
            ];
        }
    }
    
    /**
     * Get system prompt based on context
     */
    private function getSystemPrompt(string $context): string
    {
        $basePrompt = "Kamu adalah AI Assistant untuk Smart Copy SMK - aplikasi pembuat caption jualan untuk UMKM Indonesia. Kamu membantu user dengan:
1. Cara menggunakan aplikasi
2. Tips digital marketing
3. Strategi caption yang efektif
4. Best practices untuk UMKM

Jawab dalam Bahasa Indonesia yang friendly dan mudah dipahami. Jawab singkat dan to-the-point (max 2-3 kalimat). Jika pertanyaan di luar topik aplikasi dan digital marketing, jawab: 'Maaf, saya hanya bisa membantu soal aplikasi dan digital marketing.'";
        
        $contextPrompts = [
            'landing_page' => $basePrompt . "\n\nContext: User sedang di landing page. Bantu mereka memahami fitur aplikasi dan manfaatnya.",
            'client_generator' => $basePrompt . "\n\nContext: User sedang menggunakan AI Generator. Bantu mereka membuat caption yang lebih baik.",
            'client_analytics' => $basePrompt . "\n\nContext: User sedang melihat analytics. Bantu mereka memahami metrics dan cara meningkatkan performa.",
            'general' => $basePrompt,
        ];
        
        return $contextPrompts[$context] ?? $contextPrompts['general'];
    }
    
    /**
     * Get suggested questions based on context
     */
    public function getSuggestedQuestions(string $context = 'general'): array
    {
        $suggestions = [
            'landing_page' => [
                'Apa itu Smart Copy SMK?',
                'Bagaimana cara membuat caption yang menjual?',
                'Berapa harga aplikasi ini?',
                'Apakah ada trial gratis?',
            ],
            'client_generator' => [
                'Bagaimana cara membuat caption yang viral?',
                'Apa tips untuk meningkatkan engagement?',
                'Bagaimana cara memilih hashtag yang tepat?',
                'Apa perbedaan mode simpel dan lengkap?',
            ],
            'client_analytics' => [
                'Bagaimana cara meningkatkan engagement rate?',
                'Apa itu engagement rate?',
                'Bagaimana cara membaca analytics?',
                'Apa caption terbaik saya?',
            ],
            'general' => [
                'Apa itu Smart Copy SMK?',
                'Bagaimana cara membuat caption yang baik?',
                'Apa tips digital marketing?',
                'Bagaimana cara menggunakan aplikasi?',
            ],
        ];
        
        return $suggestions[$context] ?? $suggestions['general'];
    }
    
    /**
     * Get quick tips
     */
    public function getQuickTips(string $context = 'general'): array
    {
        $tips = [
            'landing_page' => [
                '💡 Caption yang baik bisa meningkatkan penjualan hingga 300%',
                '🎯 Gunakan hook pembuka yang kuat untuk menarik perhatian',
                '📱 Sesuaikan caption dengan platform (Instagram, TikTok, Facebook)',
                '✨ Gunakan bahasa yang relatable dengan target audience',
            ],
            'client_generator' => [
                '💡 Mulai dengan hook yang menarik dalam 3 detik pertama',
                '🎯 Gunakan CTA yang jelas (Beli, DM, Follow, dll)',
                '📱 Sesuaikan tone dengan brand kamu',
                '✨ Gunakan hashtag yang relevan dan trending',
            ],
            'client_analytics' => [
                '💡 Engagement rate > 5% adalah performa bagus',
                '🎯 Posting di jam prime time (7-9 malam) untuk reach lebih besar',
                '📱 Konten video lebih tinggi engagement dibanding foto',
                '✨ Konsistensi posting lebih penting dari jumlah',
            ],
            'general' => [
                '💡 Caption yang baik adalah kunci penjualan online',
                '🎯 Pahami target audience kamu sebelum membuat caption',
                '📱 Setiap platform punya strategi berbeda',
                '✨ Test dan optimize terus untuk hasil terbaik',
            ],
        ];
        
        return $tips[$context] ?? $tips['general'];
    }
}
