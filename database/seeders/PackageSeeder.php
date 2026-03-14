<?php

namespace Database\Seeders;

use App\Models\Package;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PackageSeeder extends Seeder
{
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        Package::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $packages = [
            [
                'name'                        => 'Gratis',
                'description'                 => 'Coba semua fitur AI selama 30 hari. Tidak perlu kartu kredit.',
                'price'                       => 0,
                'yearly_price'                => 0,
                'ai_quota_monthly'            => 30,
                'caption_quota'               => 30,
                'product_description_quota'   => 5,
                'revision_limit'              => 1,
                'response_time_hours'         => 72,
                'consultation_included'       => false,
                'content_calendar_included'   => false,
                'has_trial'                   => true,
                'trial_days'                  => 30,
                'is_featured'                 => false,
                'is_active'                   => true,
                'badge_text'                  => 'COBA GRATIS',
                'badge_color'                 => 'green',
                'sort_order'                  => 1,
                'features'                    => json_encode([
                    '30 generate AI per bulan',
                    'Maks 5 generate per hari',
                    'Template dasar (50+)',
                    'Short drama & story',
                    'Caption social media',
                    'Watermark pada output',
                    'Support via email',
                ]),
            ],
            [
                'name'                        => 'Starter',
                'description'                 => 'Untuk UMKM & pebisnis solo yang baru mulai jualan online.',
                'price'                       => 49000,
                'yearly_price'                => 399000,
                'ai_quota_monthly'            => 150,
                'caption_quota'               => 150,
                'product_description_quota'   => 30,
                'revision_limit'              => 3,
                'response_time_hours'         => 48,
                'consultation_included'       => false,
                'content_calendar_included'   => false,
                'has_trial'                   => true,
                'trial_days'                  => 30,
                'is_featured'                 => false,
                'is_active'                   => true,
                'badge_text'                  => null,
                'badge_color'                 => 'blue',
                'sort_order'                  => 2,
                'features'                    => json_encode([
                    '150 generate AI per bulan',
                    '30 deskripsi produk',
                    'Semua template (200+)',
                    'Short drama & story',
                    'Bulk content calendar',
                    'Analytics dasar',
                    'Tanpa watermark',
                    'Support via WhatsApp',
                ]),
            ],
            [
                'name'                        => 'Pro',
                'description'                 => 'Untuk brand & content creator yang serius tumbuh di sosmed.',
                'price'                       => 149000,
                'yearly_price'                => 1190000,
                'ai_quota_monthly'            => 500,
                'caption_quota'               => 500,
                'product_description_quota'   => 100,
                'revision_limit'              => 10,
                'response_time_hours'         => 24,
                'consultation_included'       => true,
                'content_calendar_included'   => true,
                'has_trial'                   => true,
                'trial_days'                  => 30,
                'is_featured'                 => true,
                'is_active'                   => true,
                'badge_text'                  => 'PALING POPULER',
                'badge_color'                 => 'red',
                'sort_order'                  => 3,
                'features'                    => json_encode([
                    '500 generate AI per bulan',
                    '100 deskripsi produk',
                    'Semua template (200+)',
                    'Short drama & story',
                    'Bulk content calendar',
                    'Competitor analysis',
                    'Analytics lengkap + export PDF',
                    'Template marketplace',
                    'Brand voice management',
                    'Multi-platform optimizer',
                    'Konsultasi strategi konten',
                    'Support prioritas 24 jam',
                ]),
            ],
            [
                'name'                        => 'Bisnis',
                'description'                 => 'Untuk agency, tim marketing, dan brand yang butuh volume tinggi.',
                'price'                       => 349000,
                'yearly_price'                => 2990000,
                'ai_quota_monthly'            => 2000,
                'caption_quota'               => 2000,
                'product_description_quota'   => 500,
                'revision_limit'              => 999,
                'response_time_hours'         => 4,
                'consultation_included'       => true,
                'content_calendar_included'   => true,
                'has_trial'                   => true,
                'trial_days'                  => 30,
                'is_featured'                 => false,
                'is_active'                   => true,
                'badge_text'                  => 'TERLENGKAP',
                'badge_color'                 => 'purple',
                'sort_order'                  => 4,
                'features'                    => json_encode([
                    '2.000 generate AI per bulan',
                    '500 deskripsi produk',
                    'Semua fitur Pro',
                    'Akses API (coming soon)',
                    'White-label output',
                    'Manajemen tim (5 seat)',
                    'Dedicated account manager',
                    'Laporan performa bulanan',
                    'Custom brand voice',
                    'Support 24/7 prioritas',
                ]),
            ],
        ];

        foreach ($packages as $data) {
            Package::create($data);
        }
    }
}
