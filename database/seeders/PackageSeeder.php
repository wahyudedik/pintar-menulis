<?php

namespace Database\Seeders;

use App\Models\Package;
use Illuminate\Database\Seeder;

class PackageSeeder extends Seeder
{
    public function run(): void
    {
        Package::create([
            'name' => 'Basic',
            'description' => 'Paket untuk UMKM pemula yang baru memulai promosi digital',
            'price' => 50000,
            'caption_quota' => 20,
            'product_description_quota' => 0,
            'revision_limit' => 1,
            'response_time_hours' => 24,
            'consultation_included' => false,
            'content_calendar_included' => false,
            'is_active' => true,
        ]);

        Package::create([
            'name' => 'Professional',
            'description' => 'Paket lengkap untuk UMKM yang serius mengembangkan bisnis online',
            'price' => 150000,
            'caption_quota' => 50,
            'product_description_quota' => 5,
            'revision_limit' => 3,
            'response_time_hours' => 12,
            'consultation_included' => true,
            'content_calendar_included' => false,
            'is_active' => true,
        ]);

        Package::create([
            'name' => 'Enterprise',
            'description' => 'Paket premium dengan layanan unlimited dan prioritas tertinggi',
            'price' => 300000,
            'caption_quota' => 999,
            'product_description_quota' => 20,
            'revision_limit' => 999,
            'response_time_hours' => 6,
            'consultation_included' => true,
            'content_calendar_included' => true,
            'is_active' => true,
        ]);
    }
}
