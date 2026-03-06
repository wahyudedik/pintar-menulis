<?php

namespace Database\Seeders;

use App\Models\PaymentSetting;
use Illuminate\Database\Seeder;

class PaymentSettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Default payment methods for Smart Copy SMK
        $paymentMethods = [
            [
                'bank_name' => 'BCA',
                'account_number' => '1234567890',
                'account_name' => 'PT Smart Copy SMK',
                'qr_code_path' => null,
                'is_active' => true,
            ],
            [
                'bank_name' => 'Mandiri',
                'account_number' => '1400012345678',
                'account_name' => 'PT Smart Copy SMK',
                'qr_code_path' => null,
                'is_active' => true,
            ],
            [
                'bank_name' => 'BNI',
                'account_number' => '0987654321',
                'account_name' => 'PT Smart Copy SMK',
                'qr_code_path' => null,
                'is_active' => true,
            ],
        ];

        foreach ($paymentMethods as $method) {
            PaymentSetting::create($method);
        }

        $this->command->info('✓ Payment settings seeded successfully!');
    }
}
