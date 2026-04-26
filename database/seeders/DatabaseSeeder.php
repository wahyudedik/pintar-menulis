<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database. 
     */
    public function run(): void
    {
        $this->call([
            PackageSeeder::class,
            OperatorProfileSeeder::class,
        ]);

        // Create test users for each role
        User::factory()->create([
            'name' => 'Test Client',
            'email' => 'client@gmail.com',
            'role' => 'client',
        ]);

        // Create test operator (simple untuk testing)
        $testOperator = User::factory()->create([
            'name' => 'Test Operator',
            'email' => 'operator@gmail.com',
            'role' => 'operator',
        ]);

        // Create profile for test operator
        \App\Models\OperatorProfile::create([
            'user_id' => $testOperator->id,
            'bio' => 'Test operator untuk development & testing. Full stack copywriter.',
            'specializations' => ['Testing', 'All Categories'],
            'base_price' => 50000,
            'average_rating' => 5.0,
            'total_reviews' => 1,
            'completed_orders' => 0,
            'is_verified' => true,
            'is_available' => true,
            'verified_at' => now(),
        ]);

        User::factory()->create([
            'name' => 'Test Admin',
            'email' => 'admin@gmail.com', 
            'role' => 'admin',
        ]);

        User::factory()->create([
            'name' => 'Test Guru',
            'email' => 'guru@gmail.com',
            'role' => 'guru',
        ]);
    }
}
