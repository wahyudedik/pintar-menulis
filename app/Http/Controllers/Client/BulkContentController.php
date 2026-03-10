<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\ContentCalendar;
use App\Services\GeminiService;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class BulkContentController extends Controller
{
    protected $geminiService;

    public function __construct(GeminiService $geminiService)
    {
        $this->geminiService = $geminiService;
    }

    /**
     * Show bulk content generator page
     */
    public function index()
    {
        $calendars = auth()->user()->contentCalendars()
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('client.bulk-content.index', compact('calendars'));
    }

    /**
     * Show create form
     */
    public function create()
    {
        return view('client.bulk-content.create');
    }

    /**
     * Generate bulk content
     */
    /**
     * Generate bulk content
     */
    /**
     * Generate bulk content (Quick version with templates)
     */
    public function generate(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'duration' => 'required|in:7_days,30_days',
            'start_date' => 'required|date',
            'category' => 'required|string',
            'platform' => 'required|string',
            'tone' => 'required|string',
            'brief' => 'required|string|max:1000',
        ]);

        try {
            $days = $validated['duration'] === '7_days' ? 7 : 30;
            $startDate = Carbon::parse($validated['start_date']);
            $endDate = $startDate->copy()->addDays($days - 1);

            // Generate content items (template-based, fast!)
            $contentItems = $this->generateContentItemsTemplate(
                $days,
                $startDate,
                $validated['category'],
                $validated['platform'],
                $validated['tone'],
                $validated['brief']
            );

            // Save to database
            $calendar = ContentCalendar::create([
                'user_id' => auth()->id(),
                'title' => $validated['title'],
                'duration' => $validated['duration'],
                'start_date' => $startDate,
                'end_date' => $endDate,
                'category' => $validated['category'],
                'platform' => $validated['platform'],
                'tone' => $validated['tone'],
                'brief' => $validated['brief'],
                'content_items' => $contentItems,
                'status' => 'active',
            ]);

            // Save each content item to caption_history for ML training & analytics
            foreach ($contentItems as $item) {
                \App\Models\CaptionHistory::recordCaption(
                    auth()->id(),
                    $item['caption'],
                    [
                        'brief' => $validated['brief'] . " (Day {$item['day_number']})",
                        'category' => $validated['category'],
                        'platform' => $validated['platform'],
                        'tone' => $validated['tone'],
                    ]
                );
            }

            Log::info('Bulk content generated and saved to history', [
                'user_id' => auth()->id(),
                'days' => $days,
                'items_saved' => count($contentItems)
            ]);

            return response()->json([
                'success' => true,
                'message' => "Successfully generated {$days} days of content!",
                'calendar_id' => $calendar->id,
                'redirect' => route('bulk-content.show', $calendar),
            ]);

        } catch (\Exception $e) {
            Log::error('Bulk content generation failed: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to generate content. Please try again.',
            ], 500);
        }
    }

    /**
     * Generate content items using templates (FAST!)
     */
    protected function generateContentItemsTemplate($days, $startDate, $category, $platform, $tone, $brief)
    {
        $contentItems = [];
        $postingTimes = ['09:00', '12:00', '18:00'];

        for ($i = 0; $i < $days; $i++) {
            $date = $startDate->copy()->addDays($i);
            $dayName = $date->locale('id')->dayName;
            $timeSlot = $postingTimes[$i % 3];
            $theme = $this->getDailyTheme($i, $days, $dayName);
            $caption = $this->getTemplateCaption($theme, $brief, $category, $dayName, $platform, $tone);

            $contentItems[] = [
                'day_number' => $i + 1,
                'scheduled_date' => $date->format('Y-m-d'),
                'scheduled_time' => $timeSlot,
                'day_name' => $dayName,
                'theme' => $theme,
                'caption' => $caption,
                'status' => 'scheduled',
            ];
        }

        return $contentItems;
    }

    /**
     * Get template-based caption (instant generation!)
     */
    protected function getTemplateCaption($theme, $brief, $category, $dayName, $platform, $tone)
    {
        $briefShort = strlen($brief) > 100 ? substr($brief, 0, 100) . '...' : $brief;

        $templates = [
            'Motivasi Senin Semangat' => "💪 Selamat {$dayName}! Semangat memulai minggu baru!\n\n{$briefShort}\n\nYuk, raih kesuksesan bersama! 🌟\n\nInfo & Order: DM ya! 💬\n\n#motivasi #seninSemangat #umkm #" . strtolower(str_replace(' ', '', $category)),

            'Promo Jumat Berkah' => "🔥 PROMO {$dayName} BERKAH!\n\n{$briefShort}\n\nDiskon spesial hari ini! Jangan sampai kehabisan! 🛒\n\nOrder sekarang! 📲\n\n#promoJumat #diskon #umkm #" . strtolower(str_replace(' ', '', $category)),

            'Weekend Special' => "🎉 {$dayName} Spesial!\n\n{$briefShort}\n\nSaatnya manjain diri! Yuk order sekarang! 🛍️\n\nDM untuk info lebih lanjut! 💬\n\n#weekend #promo #umkm #" . strtolower(str_replace(' ', '', $category)),

            'Edukasi Produk' => "📚 Tahukah kamu?\n\n{$briefShort}\n\nPelajari lebih lanjut tentang produk kami! 💡\n\nDM untuk konsultasi gratis! 💬\n\n#edukasi #produk #umkm #" . strtolower(str_replace(' ', '', $category)),

            'Testimoni & Review' => "⭐⭐⭐⭐⭐\n\n\"{$briefShort}\"\n\nTerima kasih atas kepercayaannya! 🙏\n\nYuk, jadi customer selanjutnya! 💬\n\n#testimoni #review #umkm #" . strtolower(str_replace(' ', '', $category)),

            'Tips & Tricks' => "💡 Tips {$dayName}:\n\n{$briefShort}\n\nSemoga bermanfaat! Save & share ya! 📌\n\nFollow untuk tips lainnya! 👆\n\n#tips #tricks #umkm #" . strtolower(str_replace(' ', '', $category)),

            'Promo & Diskon' => "🎁 PROMO HARI INI!\n\n{$briefShort}\n\nHarga spesial terbatas! Buruan order! 🛒\n\nDM sekarang! 📲\n\n#promo #diskon #umkm #" . strtolower(str_replace(' ', '', $category)),
        ];

        $caption = $templates[$theme] ?? "✨ {$theme}\n\n{$briefShort}\n\nInfo & Order: DM ya! 💬\n\n#umkm #jualanonline #" . strtolower(str_replace(' ', '', $category)) . " #indonesia";

        return $caption;
    }



    /**
     * Generate content items for each day
     */
    protected function generateContentItems($days, $startDate, $category, $platform, $tone, $brief)
    {
        $contentItems = [];
        $postingTimes = ['09:00', '12:00', '18:00']; // Pagi, Siang, Malam

        for ($i = 0; $i < $days; $i++) {
            $date = $startDate->copy()->addDays($i);
            $dayName = $date->locale('id')->dayName;
            $timeSlot = $postingTimes[$i % 3]; // Rotate posting times

            // Generate theme for the day
            $theme = $this->getDailyTheme($i, $days, $dayName);

            // Generate caption using AI
            $caption = $this->generateDailyCaption(
                $category,
                $platform,
                $tone,
                $brief,
                $theme,
                $dayName
            );

            $contentItems[] = [
                'day_number' => $i + 1,
                'scheduled_date' => $date->format('Y-m-d'),
                'scheduled_time' => $timeSlot,
                'day_name' => $dayName,
                'theme' => $theme,
                'caption' => $caption,
                'status' => 'scheduled',
            ];
        }

        return $contentItems;
    }

    /**
     * Get theme for each day
     */
    protected function getDailyTheme($dayIndex, $totalDays, $dayName)
    {
        $themes = [
            'Motivasi & Inspirasi',
            'Edukasi Produk',
            'Testimoni & Review',
            'Behind The Scenes',
            'Tips & Tricks',
            'Promo & Diskon',
            'Fun Facts',
            'Customer Story',
            'Product Showcase',
            'FAQ & Q&A',
            'Giveaway & Contest',
            'Trending Topic',
            'Seasonal Content',
            'User Generated Content',
        ];

        // Special themes for specific days
        if ($dayName === 'Jumat') {
            return 'Promo Jumat Berkah';
        } elseif ($dayName === 'Senin') {
            return 'Motivasi Senin Semangat';
        } elseif ($dayName === 'Sabtu' || $dayName === 'Minggu') {
            return 'Weekend Special';
        }

        return $themes[$dayIndex % count($themes)];
    }

    /**
     * Generate caption for specific day
     */
    protected function generateDailyCaption($category, $platform, $tone, $brief, $theme, $dayName)
    {
        // Shorter, more efficient prompt
        $prompt = "Buat caption {$platform} untuk {$category}.

Brief: {$brief}
Tema: {$theme} ({$dayName})
Tone: {$tone}

Format:
- Max 500 karakter
- Include emoji
- 5 hashtag relevan
- CTA jelas

Caption:";

        try {
            $result = $this->geminiService->generateCopywriting([
                'type' => 'bulk_content',
                'brief' => $prompt,
                'tone' => $tone,
                'platform' => $platform,
            ]);

            return $result['caption'] ?? $this->getFallbackCaption($theme, $brief, $category);
        } catch (\Exception $e) {
            Log::error('Caption generation failed: ' . $e->getMessage());
            return $this->getFallbackCaption($theme, $brief, $category);
        }
    }

    /**
     * Get fallback caption if AI fails
     */
    protected function getFallbackCaption($theme, $brief, $category)
    {
        return "{$theme} 🎯\n\n{$brief}\n\n💡 Info lebih lanjut, DM ya!\n\n#umkm #jualanonline #" . strtolower(str_replace(' ', '', $category)) . " #indonesia #bisnisonline";
    }

    /**
     * Show calendar detail
     */
    public function show(ContentCalendar $calendar)
    {
        // Check ownership
        if ($calendar->user_id !== auth()->id()) {
            abort(403);
        }

        return view('client.bulk-content.show', compact('calendar'));
    }

    /**
     * Export to Excel/CSV
     */
    public function export(ContentCalendar $calendar, $format = 'csv')
    {
        // Check ownership
        if ($calendar->user_id !== auth()->id()) {
            abort(403);
        }

        $filename = "content-calendar-{$calendar->id}-" . date('Y-m-d') . ".{$format}";

        if ($format === 'csv') {
            return $this->exportCsv($calendar, $filename);
        } else {
            return $this->exportExcel($calendar, $filename);
        }
    }

    /**
     * Export to CSV
     */
    protected function exportCsv($calendar, $filename)
    {
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function() use ($calendar) {
            $file = fopen('php://output', 'w');
            
            // Header
            fputcsv($file, ['No', 'Tanggal', 'Hari', 'Jam Posting', 'Tema', 'Caption', 'Status']);

            // Data
            foreach ($calendar->content_items as $item) {
                fputcsv($file, [
                    $item['day_number'],
                    $item['scheduled_date'],
                    $item['day_name'],
                    $item['scheduled_time'],
                    $item['theme'],
                    $item['caption'],
                    $item['status'],
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Delete calendar
     */
    public function destroy(ContentCalendar $calendar)
    {
        // Check ownership
        if ($calendar->user_id !== auth()->id()) {
            abort(403);
        }

        $calendar->delete();

        return redirect()->route('bulk-content.index')
            ->with('success', 'Content calendar deleted successfully');
    }

    /**
     * Update single content item
     */
    public function updateContent(Request $request, ContentCalendar $calendar, $dayNumber)
    {
        // Check ownership
        if ($calendar->user_id !== auth()->id()) {
            abort(403);
        }

        $validated = $request->validate([
            'caption' => 'required|string',
            'scheduled_time' => 'required|string',
        ]);

        $contentItems = $calendar->content_items;
        
        foreach ($contentItems as $key => $item) {
            if ($item['day_number'] == $dayNumber) {
                $contentItems[$key]['caption'] = $validated['caption'];
                $contentItems[$key]['scheduled_time'] = $validated['scheduled_time'];
                break;
            }
        }

        $calendar->update(['content_items' => $contentItems]);

        return response()->json([
            'success' => true,
            'message' => 'Content updated successfully',
        ]);
    }
}
