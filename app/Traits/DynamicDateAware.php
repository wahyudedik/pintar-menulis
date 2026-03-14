<?php

namespace App\Traits;

use App\Services\DynamicDateService;

trait DynamicDateAware
{
    /**
     * Process prompt with dynamic date replacements
     */
    protected function processPromptWithDynamicDates(string $prompt): string
    {
        return DynamicDateService::replaceDatePlaceholders($prompt);
    }

    /**
     * Get current year for AI prompts
     */
    protected function getCurrentYear(): int
    {
        return DynamicDateService::getCurrentYear();
    }

    /**
     * Get year context for AI prompts
     */
    protected function getYearContext(): array
    {
        return DynamicDateService::getYearContext();
    }

    /**
     * Get year-aware content suggestions
     */
    protected function getYearAwareContentSuggestions(): array
    {
        return DynamicDateService::getYearAwareContentSuggestions();
    }

    /**
     * Build year-aware prompt with context
     */
    protected function buildYearAwarePrompt(string $basePrompt, array $additionalContext = []): string
    {
        $yearContext = $this->getYearContext();
        
        // Add year context to prompt
        $contextPrompt = $basePrompt . "\n\nCONTEXT TAHUN:\n";
        $contextPrompt .= "- Tahun saat ini: {$yearContext['current_year']}\n";
        $contextPrompt .= "- Tahun akademik: {$yearContext['academic_year']}\n";
        
        // Add nearby holidays if any
        if (!empty($yearContext['nearby_holidays'])) {
            $contextPrompt .= "- Event terdekat: ";
            $upcomingEvents = array_filter($yearContext['nearby_holidays'], fn($h) => $h['is_upcoming']);
            if (!empty($upcomingEvents)) {
                $eventNames = array_map(fn($h) => $h['name'], array_slice($upcomingEvents, 0, 3));
                $contextPrompt .= implode(', ', $eventNames) . "\n";
            } else {
                $contextPrompt .= "Tidak ada event khusus\n";
            }
        }
        
        $contextPrompt .= "\nPastikan semua konten menggunakan tahun yang tepat dan relevan dengan konteks saat ini.\n";
        
        return $this->processPromptWithDynamicDates($contextPrompt);
    }

    /**
     * Validate and update year references in generated content
     */
    protected function validateYearReferences(string $content): string
    {
        $currentYear = $this->getCurrentYear();
        
        // Replace common outdated year patterns
        $patterns = [
            '/\b(20[0-2][0-9])\b/' => function($matches) use ($currentYear) {
                $year = (int)$matches[1];
                // Only replace if it's clearly outdated (more than 2 years old)
                if ($year < $currentYear - 1) {
                    return (string)$currentYear;
                }
                return $matches[1];
            }
        ];
        
        foreach ($patterns as $pattern => $replacement) {
            if (is_callable($replacement)) {
                $content = preg_replace_callback($pattern, $replacement, $content);
            } else {
                $content = preg_replace($pattern, $replacement, $content);
            }
        }
        
        return $content;
    }

    /**
     * Add year-aware hashtags to content
     */
    protected function addYearAwareHashtags(array $hashtags): array
    {
        $currentYear = $this->getCurrentYear();
        $yearAwareHashtags = [];
        
        foreach ($hashtags as $hashtag) {
            $yearAwareHashtags[] = $hashtag;
            
            // Add year-specific versions for trending hashtags
            if (in_array($hashtag, ['#Trending', '#Viral', '#Update', '#Tips', '#Guide'])) {
                $yearAwareHashtags[] = $hashtag . $currentYear;
            }
        }
        
        return array_unique($yearAwareHashtags);
    }

    /**
     * Get seasonal content suggestions based on current date
     */
    protected function getSeasonalContentSuggestions(): array
    {
        $nearbyHolidays = DynamicDateService::getNearbyHolidays();
        $suggestions = [];
        
        foreach ($nearbyHolidays as $holiday) {
            if ($holiday['is_upcoming'] && $holiday['days_until'] <= 30) {
                $suggestions[] = [
                    'event' => $holiday['name'],
                    'date' => $holiday['date'],
                    'days_until' => $holiday['days_until'],
                    'content_ideas' => $this->getHolidayContentIdeas($holiday['name'])
                ];
            }
        }
        
        return $suggestions;
    }

    /**
     * Get content ideas for specific holidays
     */
    private function getHolidayContentIdeas(string $holidayName): array
    {
        $ideas = [
            'Hari Kartini' => [
                'Inspirasi wanita pengusaha',
                'Tips bisnis untuk perempuan',
                'Cerita sukses UMKM wanita',
                'Produk khusus wanita'
            ],
            'Hari Kemerdekaan' => [
                'Produk lokal Indonesia',
                'Promo kemerdekaan',
                'Bangga buatan Indonesia',
                'Sejarah brand lokal'
            ],
            'Hari Pendidikan Nasional' => [
                'Tips belajar bisnis',
                'Edukasi produk',
                'Workshop gratis',
                'Skill development'
            ],
            'Ramadan' => [
                'Menu sahur & buka puasa',
                'Promo Ramadan',
                'Konten spiritual',
                'Charity campaign'
            ],
            'Lebaran' => [
                'Outfit lebaran',
                'Hampers lebaran',
                'Promo THR',
                'Family gathering'
            ]
        ];
        
        return $ideas[$holidayName] ?? [
            'Konten spesial ' . $holidayName,
            'Promo ' . $holidayName,
            'Tips ' . $holidayName
        ];
    }
}