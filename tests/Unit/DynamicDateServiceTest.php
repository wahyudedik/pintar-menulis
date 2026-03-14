<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Services\DynamicDateService;
use Carbon\Carbon;

class DynamicDateServiceTest extends TestCase
{
    /**
     * Test current year retrieval
     */
    public function test_get_current_year()
    {
        $currentYear = DynamicDateService::getCurrentYear();
        $expectedYear = Carbon::now()->year;
        
        $this->assertEquals($expectedYear, $currentYear);
        $this->assertIsInt($currentYear);
        $this->assertGreaterThanOrEqual(2026, $currentYear);
    }

    /**
     * Test next year calculation
     */
    public function test_get_next_year()
    {
        $nextYear = DynamicDateService::getNextYear();
        $expectedNextYear = Carbon::now()->year + 1;
        
        $this->assertEquals($expectedNextYear, $nextYear);
        $this->assertGreaterThan(DynamicDateService::getCurrentYear(), $nextYear);
    }

    /**
     * Test seasonal events generation
     */
    public function test_get_seasonal_events()
    {
        $events = DynamicDateService::getSeasonalEvents();
        
        $this->assertIsArray($events);
        $this->assertNotEmpty($events);
        
        // Check that events contain current year
        $currentYear = DynamicDateService::getCurrentYear();
        foreach ($events as $event) {
            $this->assertArrayHasKey('title', $event);
            $this->assertArrayHasKey('date', $event);
            $this->assertStringContainsString((string)$currentYear, $event['date']);
        }
        
        // Check specific events exist
        $eventTitles = array_column($events, 'title');
        $this->assertContains("Ramadan {$currentYear}", $eventTitles);
        $this->assertContains('Indonesian Independence Day', $eventTitles);
    }

    /**
     * Test national days generation
     */
    public function test_get_national_days()
    {
        $nationalDays = DynamicDateService::getNationalDays();
        
        $this->assertIsArray($nationalDays);
        $this->assertNotEmpty($nationalDays);
        
        // Check that dates contain current year
        $currentYear = DynamicDateService::getCurrentYear();
        foreach ($nationalDays as $day) {
            $this->assertArrayHasKey('title', $day);
            $this->assertArrayHasKey('date', $day);
            $this->assertStringContainsString((string)$currentYear, $day['date']);
        }
        
        // Check specific national days exist
        $dayTitles = array_column($nationalDays, 'title');
        $this->assertContains('Hari Kartini', $dayTitles);
        $this->assertContains('Hari Pendidikan Nasional', $dayTitles);
    }

    /**
     * Test date placeholder replacement
     */
    public function test_replace_date_placeholders()
    {
        $currentYear = DynamicDateService::getCurrentYear();
        
        $testPrompt = "Panduan bisnis {CURRENT_YEAR} dan tips 2024 terbaru";
        $processedPrompt = DynamicDateService::replaceDatePlaceholders($testPrompt);
        
        $this->assertStringContainsString((string)$currentYear, $processedPrompt);
        $this->assertStringNotContainsString('{CURRENT_YEAR}', $processedPrompt);
        $this->assertStringNotContainsString('2024', $processedPrompt);
    }

    /**
     * Test automotive year range
     */
    public function test_get_automotive_year_range()
    {
        $range = DynamicDateService::getAutomotiveYearRange();
        $currentYear = DynamicDateService::getCurrentYear();
        
        $this->assertIsString($range);
        $this->assertStringContainsString('-', $range);
        
        // Should be in format "YYYY-YYYY"
        $this->assertMatchesRegularExpression('/^\d{4}-\d{4}$/', $range);
        
        // Extract years and verify they're reasonable
        [$startYear, $endYear] = explode('-', $range);
        $this->assertLessThan($currentYear, (int)$endYear);
        $this->assertLessThan((int)$endYear, (int)$startYear);
    }

    /**
     * Test academic year calculation
     */
    public function test_get_academic_year()
    {
        $academicYear = DynamicDateService::getAcademicYear();
        
        $this->assertIsString($academicYear);
        $this->assertStringContainsString('/', $academicYear);
        
        // Should be in format "YYYY/YYYY"
        $this->assertMatchesRegularExpression('/^\d{4}\/\d{4}$/', $academicYear);
    }

    /**
     * Test quarterly dates
     */
    public function test_get_quarterly_dates()
    {
        $quarters = DynamicDateService::getQuarterlyDates();
        $currentYear = DynamicDateService::getCurrentYear();
        
        $this->assertIsArray($quarters);
        $this->assertArrayHasKey('Q1', $quarters);
        $this->assertArrayHasKey('Q2', $quarters);
        $this->assertArrayHasKey('Q3', $quarters);
        $this->assertArrayHasKey('Q4', $quarters);
        
        // Each quarter should contain current year
        foreach ($quarters as $quarter) {
            $this->assertStringContainsString((string)$currentYear, $quarter);
        }
    }

    /**
     * Test nearby holidays detection
     */
    public function test_get_nearby_holidays()
    {
        $holidays = DynamicDateService::getNearbyHolidays();
        
        $this->assertIsArray($holidays);
        
        // Each holiday should have required fields
        foreach ($holidays as $holiday) {
            $this->assertArrayHasKey('name', $holiday);
            $this->assertArrayHasKey('date', $holiday);
            $this->assertArrayHasKey('days_until', $holiday);
            $this->assertArrayHasKey('is_upcoming', $holiday);
            
            $this->assertIsInt($holiday['days_until']);
            $this->assertIsBool($holiday['is_upcoming']);
        }
    }

    /**
     * Test year context generation
     */
    public function test_get_year_context()
    {
        $context = DynamicDateService::getYearContext();
        
        $this->assertIsArray($context);
        $this->assertArrayHasKey('current_year', $context);
        $this->assertArrayHasKey('next_year', $context);
        $this->assertArrayHasKey('academic_year', $context);
        $this->assertArrayHasKey('automotive_range', $context);
        $this->assertArrayHasKey('quarterly_dates', $context);
        $this->assertArrayHasKey('nearby_holidays', $context);
        
        // Verify data types
        $this->assertIsInt($context['current_year']);
        $this->assertIsInt($context['next_year']);
        $this->assertIsString($context['academic_year']);
        $this->assertIsString($context['automotive_range']);
        $this->assertIsArray($context['quarterly_dates']);
        $this->assertIsArray($context['nearby_holidays']);
    }

    /**
     * Test year-aware content suggestions
     */
    public function test_get_year_aware_content_suggestions()
    {
        $suggestions = DynamicDateService::getYearAwareContentSuggestions();
        $currentYear = DynamicDateService::getCurrentYear();
        
        $this->assertIsArray($suggestions);
        $this->assertNotEmpty($suggestions);
        
        // Should contain current year in suggestions
        $suggestionsText = implode(' ', $suggestions);
        $this->assertStringContainsString((string)$currentYear, $suggestionsText);
    }

    /**
     * Test that years are always current or future
     */
    public function test_years_are_not_outdated()
    {
        $currentYear = Carbon::now()->year;
        
        // Test all year-related methods
        $this->assertGreaterThanOrEqual($currentYear, DynamicDateService::getCurrentYear());
        $this->assertGreaterThan($currentYear, DynamicDateService::getNextYear());
        
        // Test that seasonal events don't use outdated years
        $events = DynamicDateService::getSeasonalEvents();
        foreach ($events as $event) {
            $this->assertStringNotContainsString('2024', $event['date']);
            $this->assertStringNotContainsString('2023', $event['date']);
            $this->assertStringNotContainsString('2022', $event['date']);
        }
    }

    /**
     * Test placeholder replacement with multiple placeholders
     */
    public function test_multiple_placeholder_replacement()
    {
        $currentYear = DynamicDateService::getCurrentYear();
        $nextYear = DynamicDateService::getNextYear();
        
        $prompt = "Tahun {CURRENT_YEAR} sudah berakhir, sekarang {NEXT_YEAR}. Update 2024 ke tahun terbaru.";
        $processed = DynamicDateService::replaceDatePlaceholders($prompt);
        
        $this->assertStringContainsString((string)$currentYear, $processed);
        $this->assertStringContainsString((string)$nextYear, $processed);
        $this->assertStringNotContainsString('{CURRENT_YEAR}', $processed);
        $this->assertStringNotContainsString('{NEXT_YEAR}', $processed);
        $this->assertStringNotContainsString('2024', $processed);
    }
}