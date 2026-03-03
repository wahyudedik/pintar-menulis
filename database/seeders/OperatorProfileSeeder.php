<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\OperatorProfile;
use Illuminate\Database\Seeder;

class OperatorProfileSeeder extends Seeder
{
    public function run(): void
    {
        // Create sample operators with profiles
        $operators = [
            [
                'name' => 'Budi Santoso',
                'email' => 'budi@operator.com',
                'bio' => 'Copywriter berpengalaman 5+ tahun. Spesialis social media & ads. Sudah handle 200+ project untuk UMKM.',
                'specializations' => ['Social Media', 'Ads', 'Marketplace'],
                'base_price' => 75000,
                'rating' => 4.9,
                'reviews' => 156,
                'completed_orders' => 234,
            ],
            [
                'name' => 'Siti Nurhaliza',
                'email' => 'siti@operator.com',
                'bio' => 'Expert dalam UX Writing & Website Copy. Lulusan Sastra Indonesia. Portfolio: 100+ website.',
                'specializations' => ['UX Writing', 'Website', 'Landing Page'],
                'base_price' => 150000,
                'rating' => 4.8,
                'reviews' => 89,
                'completed_orders' => 145,
            ],
            [
                'name' => 'Ahmad Fauzi',
                'email' => 'ahmad@operator.com',
                'bio' => 'Spesialis Email Marketing & Proposal. Conversion rate tinggi. Fast response.',
                'specializations' => ['Email Marketing', 'Proposal', 'Company Profile'],
                'base_price' => 100000,
                'rating' => 4.7,
                'reviews' => 67,
                'completed_orders' => 98,
            ],
            [
                'name' => 'Dewi Lestari',
                'email' => 'dewi@operator.com',
                'bio' => 'Personal Branding Specialist. Membantu profesional & entrepreneur membangun brand yang kuat.',
                'specializations' => ['Personal Branding', 'LinkedIn', 'Bio Instagram'],
                'base_price' => 120000,
                'rating' => 4.9,
                'reviews' => 112,
                'completed_orders' => 178,
            ],
            [
                'name' => 'Rudi Hartono',
                'email' => 'rudi@operator.com',
                'bio' => 'Marketplace copywriter. Spesialis Shopee, Tokopedia, Lazada. Tingkatkan conversion rate produk Anda.',
                'specializations' => ['Marketplace', 'Product Description', 'SEO'],
                'base_price' => 60000,
                'rating' => 4.6,
                'reviews' => 203,
                'completed_orders' => 312,
            ],
        ];

        foreach ($operators as $operatorData) {
            $user = User::create([
                'name' => $operatorData['name'],
                'email' => $operatorData['email'],
                'password' => bcrypt('password'),
                'role' => 'operator',
            ]);

            OperatorProfile::create([
                'user_id' => $user->id,
                'bio' => $operatorData['bio'],
                'specializations' => $operatorData['specializations'],
                'base_price' => $operatorData['base_price'],
                'average_rating' => $operatorData['rating'],
                'total_reviews' => $operatorData['reviews'],
                'completed_orders' => $operatorData['completed_orders'],
                'is_verified' => true,
                'is_available' => true,
                'verified_at' => now(),
            ]);
        }
    }
}
