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
        ]);

        // User::factory(10)->create();

        User::factory()->create([
            'name' => 'Test Client',
            'email' => 'client@gmail.com',
            'role' => 'client',
        ]);

        User::factory()->create([
            'name' => 'Test Operator',
            'email' => 'operator@gmail.com',
            'role' => 'operator',
        ]);
    }
}
