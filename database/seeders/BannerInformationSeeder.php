<?php

namespace Database\Seeders;

use App\Models\BannerInformation;
use Illuminate\Database\Seeder;

class BannerInformationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $banners = [
            [
                'type' => 'landing',
                'title' => 'Selamat Datang di Smart Copy SMK! 🎉',
                'content' => '<p>Halo! Terima kasih sudah mengunjungi <strong>Smart Copy SMK</strong> - aplikasi pembuat caption jualan otomatis khusus untuk UMKM Indonesia.</p>
                <p><strong>Fitur Unggulan:</strong></p>
                <ul>
                    <li>✨ 200+ Jenis Konten untuk 23 Platform Sosmed</li>
                    <li>🎯 12 Industry Presets khusus UMKM</li>
                    <li>🗣️ Bahasa UMKM + 5 Bahasa Daerah</li>
                    <li>💾 Brand Voice Saver</li>
                    <li>📊 Analytics Tracking</li>
                </ul>
                <p>Daftar sekarang dan dapatkan <strong>GRATIS 5 variasi caption per hari!</strong></p>',
                'is_active' => false,
            ],
            [
                'type' => 'client',
                'title' => 'Selamat Datang di Dashboard Client! 👋',
                'content' => '<p>Halo <strong>Client</strong>! Selamat datang di dashboard Anda.</p>
                <p><strong>Yang bisa Anda lakukan:</strong></p>
                <ul>
                    <li>🤖 Generate caption dengan AI Generator</li>
                    <li>📊 Track performa caption di Analytics</li>
                    <li>💾 Simpan Brand Voice favorit</li>
                    <li>📝 Lihat history caption</li>
                    <li>⭐ Rate caption untuk improve AI</li>
                </ul>
                <p>Mulai generate caption pertama Anda sekarang! 🚀</p>',
                'is_active' => false,
            ],
            [
                'type' => 'operator',
                'title' => 'Selamat Datang di Dashboard Operator! ⚙️',
                'content' => '<p>Halo <strong>Operator</strong>! Terima kasih sudah bergabung dengan tim kami.</p>
                <p><strong>Tugas Anda:</strong></p>
                <ul>
                    <li>📋 Ambil order dari queue</li>
                    <li>✍️ Buat copywriting berkualitas</li>
                    <li>⏰ Selesaikan sebelum deadline</li>
                    <li>💰 Dapatkan earnings dari setiap order</li>
                    <li>⭐ Tingkatkan rating Anda</li>
                </ul>
                <p>Semangat bekerja! 💪</p>',
                'is_active' => false,
            ],
            [
                'type' => 'guru',
                'title' => 'Selamat Datang di Dashboard Guru! 🎓',
                'content' => '<p>Halo <strong>Guru</strong>! Terima kasih sudah membantu melatih AI kami.</p>
                <p><strong>Tanggung Jawab Anda:</strong></p>
                <ul>
                    <li>🎯 Review dan rate caption quality</li>
                    <li>📚 Train AI dengan data berkualitas</li>
                    <li>📊 Monitor ML analytics</li>
                    <li>🔄 Improve model performance</li>
                    <li>✅ Validate training data</li>
                </ul>
                <p>Mari kita buat AI yang lebih pintar! 🧠</p>',
                'is_active' => false,
            ],
        ];

        foreach ($banners as $banner) {
            BannerInformation::updateOrCreate(
                ['type' => $banner['type']],
                $banner
            );
        }
    }
}
