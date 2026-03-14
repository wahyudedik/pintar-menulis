<?php

namespace App\Console\Commands;

use App\Services\WhatsAppService;
use App\Models\User;
use App\Models\WhatsAppMessage;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class SendDailyWhatsAppContent extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'wa:daily {--phone= : Send to specific phone number} {--test : Test mode}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send daily content ideas via WhatsApp to subscribed users';

    private $whatsappService;

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->whatsappService = app(WhatsAppService::class);
        
        $this->info('🚀 Starting daily WhatsApp content broadcast...');

        try {
            // Check if WhatsApp service is enabled
            if (!config('services.whatsapp.enabled')) {
                $this->error('❌ WhatsApp service is disabled');
                return 1;
            }

            // Check device status
            $deviceStatus = $this->whatsappService->getDeviceStatus();
            if (!$deviceStatus['success'] || !($deviceStatus['connected'] ?? false)) {
                $this->error('❌ WhatsApp device is not connected');
                Log::error('WhatsApp device not connected for daily broadcast', $deviceStatus);
                return 1;
            }

            $this->info('✅ WhatsApp device is connected');

            // Get target phone numbers
            $phoneNumbers = $this->getTargetPhoneNumbers();
            
            if (empty($phoneNumbers)) {
                $this->warn('⚠️ No phone numbers found for broadcast');
                return 0;
            }

            $this->info("📱 Found " . count($phoneNumbers) . " phone numbers for broadcast");

            // Send daily content to each number
            $successCount = 0;
            $failCount = 0;

            foreach ($phoneNumbers as $index => $phoneNumber) {
                $this->info("📤 Sending to {$phoneNumber} (" . ($index + 1) . "/" . count($phoneNumbers) . ")");

                try {
                    $result = $this->whatsappService->sendDailyContentIdeas($phoneNumber, [
                        'business_type' => 'UMKM',
                        'target_audience' => 'Gen Z Indonesia'
                    ]);

                    if ($result['success']) {
                        $successCount++;
                        $this->info("✅ Sent to {$phoneNumber}");

                        // Record outgoing message
                        WhatsAppMessage::recordOutgoing([
                            'phone_number' => $phoneNumber,
                            'message_type' => 'text',
                            'message_content' => 'Daily content ideas broadcast',
                            'metadata' => [
                                'broadcast_type' => 'daily_content',
                                'sent_at' => now()->toISOString(),
                                'command_execution' => true
                            ],
                            'status' => 'sent'
                        ]);
                    } else {
                        $failCount++;
                        $this->error("❌ Failed to send to {$phoneNumber}: " . ($result['message'] ?? 'Unknown error'));
                    }

                    // Add delay between messages to avoid rate limiting
                    if ($index < count($phoneNumbers) - 1) {
                        $delay = $this->option('test') ? 1 : 3; // 1 second in test mode, 3 seconds in production
                        $this->info("⏳ Waiting {$delay} seconds...");
                        sleep($delay);
                    }

                } catch (\Exception $e) {
                    $failCount++;
                    $this->error("❌ Exception sending to {$phoneNumber}: " . $e->getMessage());
                    Log::error('Daily WhatsApp broadcast failed for number', [
                        'phone' => $phoneNumber,
                        'error' => $e->getMessage()
                    ]);
                }
            }

            // Summary
            $this->info("\n📊 Broadcast Summary:");
            $this->info("✅ Successful: {$successCount}");
            $this->info("❌ Failed: {$failCount}");
            $this->info("📱 Total: " . count($phoneNumbers));

            Log::info('Daily WhatsApp content broadcast completed', [
                'success_count' => $successCount,
                'fail_count' => $failCount,
                'total_count' => count($phoneNumbers)
            ]);

            return 0;

        } catch (\Exception $e) {
            $this->error('❌ Daily broadcast failed: ' . $e->getMessage());
            Log::error('Daily WhatsApp broadcast failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return 1;
        }
    }

    /**
     * Get target phone numbers for broadcast
     */
    private function getTargetPhoneNumbers(): array
    {
        // If specific phone number is provided
        if ($this->option('phone')) {
            return [$this->option('phone')];
        }

        // If test mode, use a limited set
        if ($this->option('test')) {
            return [
                '6281234567890', // Replace with test numbers
                '6289876543210'
            ];
        }

        // Get phone numbers from WhatsApp messages (users who have interacted)
        $activePhoneNumbers = WhatsAppMessage::select('phone_number')
            ->where('created_at', '>=', now()->subDays(30)) // Active in last 30 days
            ->groupBy('phone_number')
            ->havingRaw('COUNT(*) >= 2') // At least 2 interactions
            ->pluck('phone_number')
            ->toArray();

        // TODO: Add phone numbers from user preferences/subscriptions
        // This could be from a separate subscription table or user profile settings

        return $activePhoneNumbers;
    }

    /**
     * Get users who have opted in for daily WhatsApp content
     */
    private function getSubscribedUsers(): array
    {
        // TODO: Implement user subscription system
        // For now, return users who have WhatsApp interactions
        
        return User::whereHas('whatsappMessages', function ($query) {
            $query->where('created_at', '>=', now()->subDays(7));
        })->pluck('phone')->filter()->toArray();
    }
}
