<?php

namespace App\Services;

use Carbon\Carbon;

class DynamicDateService
{
    /**
     * Get current year
     */
    public static function getCurrentYear(): int
    {
        return Carbon::now()->year;
    }

    /**
     * Get dynamic seasonal events for current year
     */
    public static function getSeasonalEvents(): array
    {
        $currentYear = self::getCurrentYear();
        
        return [
            [
                'id' => 1,
                'title' => "Ramadan {$currentYear}",
                'date' => self::getRamadanDate($currentYear),
                'category' => 'religious',
                'daysLeft' => self::calculateDaysLeft(self::getRamadanStartDate($currentYear)),
                'description' => 'Bulan suci Ramadan dengan berbagai peluang konten dan promo',
                'icon' => '🌙',
                'hashtags' => ['#Ramadan', '#BulanSuci', '#Puasa', '#Berkah'],
                'contentIdeas' => ['Sahur Ideas', 'Iftar Menu', 'Spiritual Content', 'Charity Campaign']
            ],
            [
                'id' => 2,
                'title' => 'Lebaran/Eid Mubarak',
                'date' => self::getLebaranDate($currentYear),
                'category' => 'religious',
                'daysLeft' => self::calculateDaysLeft(self::getLebaranStartDate($currentYear)),
                'description' => 'Hari Raya Idul Fitri dengan tradisi mudik dan berkumpul keluarga',
                'icon' => '🎉',
                'hashtags' => ['#Lebaran', '#EidMubarak', '#Mudik', '#Keluarga'],
                'contentIdeas' => ['Lebaran Outfit', 'Mudik Tips', 'Family Gathering', 'THR Campaign']
            ],
            [
                'id' => 3,
                'title' => 'Back to School',
                'date' => "Juli {$currentYear}",
                'category' => 'education',
                'daysLeft' => self::calculateDaysLeft(Carbon::create($currentYear, 7, 15)),
                'description' => 'Musim kembali ke sekolah dengan kebutuhan perlengkapan baru',
                'icon' => '🎒',
                'hashtags' => ['#BackToSchool', '#SekolahBaru', '#Perlengkapan', '#Pendidikan'],
                'contentIdeas' => ['School Supplies', 'Study Tips', 'Uniform Fashion', 'Parent Guide']
            ],
            [
                'id' => 4,
                'title' => 'Indonesian Independence Day',
                'date' => "17 Agustus {$currentYear}",
                'category' => 'national',
                'daysLeft' => self::calculateDaysLeft(Carbon::create($currentYear, 8, 17)),
                'description' => 'Hari Kemerdekaan Indonesia dengan semangat nasionalisme',
                'icon' => '🇮🇩',
                'hashtags' => ['#Merdeka', '#Indonesia', '#Kemerdekaan', '#MerahPutih'],
                'contentIdeas' => ['Patriotic Content', 'Local Pride', 'Indonesian Products', 'Unity Campaign']
            ]
        ];
    }

    /**
     * Get dynamic national days for current year
     */
    public static function getNationalDays(): array
    {
        $currentYear = self::getCurrentYear();
        
        return [
            [
                'id' => 1,
                'title' => 'Hari Kartini',
                'date' => "21 April {$currentYear}",
                'category' => 'national',
                'description' => 'Memperingati perjuangan R.A. Kartini untuk emansipasi wanita',
                'icon' => '👩',
                'hashtags' => ['#HariKartini', '#WomenEmpowerment', '#Emansipasi', '#PerempuanIndonesia']
            ],
            [
                'id' => 2,
                'title' => 'Hari Pendidikan Nasional',
                'date' => "2 Mei {$currentYear}",
                'category' => 'education',
                'description' => 'Memperingati hari lahir Ki Hajar Dewantara, Bapak Pendidikan Indonesia',
                'icon' => '📚',
                'hashtags' => ['#HardikNas', '#Pendidikan', '#KiHajarDewantara', '#BelajarSepanjangHayat']
            ],
            [
                'id' => 3,
                'title' => 'Hari Kebangkitan Nasional',
                'date' => "20 Mei {$currentYear}",
                'category' => 'national',
                'description' => 'Memperingati kebangkitan semangat kebangsaan Indonesia',
                'icon' => '🌅',
                'hashtags' => ['#HariKebangkitanNasional', '#SemangitKebangsaan', '#Indonesia', '#Patriotisme']
            ],
            [
                'id' => 4,
                'title' => 'Hari Lingkungan Hidup Sedunia',
                'date' => "5 Juni {$currentYear}",
                'category' => 'environment',
                'description' => 'Kampanye global untuk kesadaran dan aksi lingkungan hidup',
                'icon' => '🌍',
                'hashtags' => ['#WorldEnvironmentDay', '#LingkunganHidup', '#GoGreen', '#SustainableLiving']
            ],
            [
                'id' => 5,
                'title' => 'Hari Sumpah Pemuda',
                'date' => "28 Oktober {$currentYear}",
                'category' => 'national',
                'description' => 'Memperingati ikrar Sumpah Pemuda 1928',
                'icon' => '🇮🇩',
                'hashtags' => ['#SumpahPemuda', '#PemudaIndonesia', '#SatuBangsa', '#BhinekaTunggalIka']
            ],
            [
                'id' => 6,
                'title' => 'Hari Pahlawan',
                'date' => "10 November {$currentYear}",
                'category' => 'national',
                'description' => 'Mengenang jasa para pahlawan Indonesia',
                'icon' => '🏅',
                'hashtags' => ['#HariPahlawan', '#JasaPahlawan', '#Indonesia', '#Kemerdekaan']
            ]
        ];
    }

    /**
     * Calculate Ramadan date for given year (approximate)
     */
    private static function getRamadanDate(int $year): string
    {
        // Ramadan moves ~11 days earlier each year
        // 2024: March 11 - April 9
        // This is approximate - in real app, use Islamic calendar API
        $baseYear = 2024;
        $baseStart = Carbon::create(2024, 3, 11);
        
        $yearDiff = $year - $baseYear;
        $dayShift = $yearDiff * -11; // Moves earlier each year
        
        $ramadanStart = $baseStart->copy()->addDays($dayShift);
        $ramadanEnd = $ramadanStart->copy()->addDays(29);
        
        // Ensure we use the correct year in the output
        return $ramadanStart->format('j M') . ' - ' . $ramadanEnd->format('j M') . ' ' . $year;
    }

    /**
     * Get Ramadan start date for calculation
     */
    private static function getRamadanStartDate(int $year): Carbon
    {
        $baseYear = 2024;
        $baseStart = Carbon::create(2024, 3, 11);
        
        $yearDiff = $year - $baseYear;
        $dayShift = $yearDiff * -11;
        
        return $baseStart->copy()->addDays($dayShift);
    }

    /**
     * Calculate Lebaran date for given year (approximate)
     */
    private static function getLebaranDate(int $year): string
    {
        $ramadanStart = self::getRamadanStartDate($year);
        $lebaranStart = $ramadanStart->copy()->addDays(30);
        $lebaranEnd = $lebaranStart->copy()->addDay();
        
        return $lebaranStart->format('j') . '-' . $lebaranEnd->format('j M') . ' ' . $year;
    }

    /**
     * Get Lebaran start date for calculation
     */
    private static function getLebaranStartDate(int $year): Carbon
    {
        $ramadanStart = self::getRamadanStartDate($year);
        return $ramadanStart->copy()->addDays(30);
    }

    /**
     * Calculate days left until a date
     */
    private static function calculateDaysLeft(Carbon $targetDate): int
    {
        $now = Carbon::now();
        $diff = $now->diffInDays($targetDate, false);
        
        return max(0, (int) $diff); // Don't return negative days, cast to int
    }

    /**
     * Get dynamic year range for automotive compatibility
     */
    public static function getAutomotiveYearRange(): string
    {
        $currentYear = self::getCurrentYear();
        $startYear = $currentYear - 11; // 11 years ago
        $endYear = $currentYear - 6;    // 6 years ago
        
        return "{$startYear}-{$endYear}";
    }

    /**
     * Get current year for content generation
     */
    public static function getCurrentYearForContent(): string
    {
        return (string) self::getCurrentYear();
    }

    /**
     * Get next year for planning content
     */
    public static function getNextYear(): int
    {
        return self::getCurrentYear() + 1;
    }

    /**
     * Get academic year (current year if before July, next year if after)
     */
    public static function getAcademicYear(): string
    {
        $currentYear = self::getCurrentYear();
        $currentMonth = Carbon::now()->month;
        
        if ($currentMonth >= 7) {
            // After July = new academic year
            return $currentYear . '/' . ($currentYear + 1);
        } else {
            // Before July = previous academic year
            return ($currentYear - 1) . '/' . $currentYear;
        }
    }

    /**
     * Get quarterly planning dates
     */
    public static function getQuarterlyDates(): array
    {
        $currentYear = self::getCurrentYear();
        
        return [
            'Q1' => "Januari - Maret {$currentYear}",
            'Q2' => "April - Juni {$currentYear}",
            'Q3' => "Juli - September {$currentYear}",
            'Q4' => "Oktober - Desember {$currentYear}"
        ];
    }

    /**
     * Check if current date is near a major Indonesian holiday
     */
    public static function getNearbyHolidays(): array
    {
        $now = Carbon::now();
        $currentYear = self::getCurrentYear();
        $nearbyHolidays = [];
        
        $holidays = [
            ['name' => 'Tahun Baru', 'date' => Carbon::create($currentYear, 1, 1)],
            ['name' => 'Hari Kartini', 'date' => Carbon::create($currentYear, 4, 21)],
            ['name' => 'Hari Buruh', 'date' => Carbon::create($currentYear, 5, 1)],
            ['name' => 'Hari Pendidikan Nasional', 'date' => Carbon::create($currentYear, 5, 2)],
            ['name' => 'Hari Kemerdekaan', 'date' => Carbon::create($currentYear, 8, 17)],
            ['name' => 'Hari Sumpah Pemuda', 'date' => Carbon::create($currentYear, 10, 28)],
            ['name' => 'Hari Pahlawan', 'date' => Carbon::create($currentYear, 11, 10)],
        ];
        
        foreach ($holidays as $holiday) {
            $daysUntil = $now->diffInDays($holiday['date'], false);
            
            // Include holidays within 30 days (past or future)
            if (abs($daysUntil) <= 30) {
                $nearbyHolidays[] = [
                    'name' => $holiday['name'],
                    'date' => $holiday['date']->format('j M Y'),
                    'days_until' => $daysUntil,
                    'is_upcoming' => $daysUntil >= 0
                ];
            }
        }
        
        return $nearbyHolidays;
    }

    /**
     * Replace year placeholders in AI prompts with current year
     */
    public static function replaceDatePlaceholders(string $prompt): string
    {
        $currentYear = self::getCurrentYear();
        $nextYear = self::getNextYear();
        $academicYear = self::getAcademicYear();
        
        $replacements = [
            '{CURRENT_YEAR}' => $currentYear,
            '{NEXT_YEAR}' => $nextYear,
            '{ACADEMIC_YEAR}' => $academicYear,
            '{AUTOMOTIVE_YEARS}' => self::getAutomotiveYearRange(),
            // Legacy support for hardcoded years
            '2024' => $currentYear,
            '2025' => $nextYear,
            '2023' => $currentYear - 1,
        ];
        
        return str_replace(array_keys($replacements), array_values($replacements), $prompt);
    }

    /**
     * Get contextual year information for AI prompts
     */
    public static function getYearContext(): array
    {
        return [
            'current_year' => self::getCurrentYear(),
            'next_year' => self::getNextYear(),
            'academic_year' => self::getAcademicYear(),
            'automotive_range' => self::getAutomotiveYearRange(),
            'quarterly_dates' => self::getQuarterlyDates(),
            'nearby_holidays' => self::getNearbyHolidays()
        ];
    }

    /**
     * Generate year-aware content suggestions
     */
    public static function getYearAwareContentSuggestions(): array
    {
        $currentYear = self::getCurrentYear();
        $nearbyHolidays = self::getNearbyHolidays();
        
        $suggestions = [
            "Panduan Lengkap {$currentYear}",
            "Tips Terbaru {$currentYear}",
            "Strategi Terkini {$currentYear}",
            "Update {$currentYear}",
            "Tren {$currentYear}",
        ];
        
        // Add holiday-specific suggestions
        foreach ($nearbyHolidays as $holiday) {
            if ($holiday['is_upcoming'] && $holiday['days_until'] <= 14) {
                $suggestions[] = "Spesial {$holiday['name']} {$currentYear}";
                $suggestions[] = "Promo {$holiday['name']}";
            }
        }
        
        return $suggestions;
    }
}