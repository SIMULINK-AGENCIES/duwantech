<?php

namespace Database\Seeders;

use App\Models\MpesaSetting;
use Illuminate\Database\Seeder;

class MpesaSettingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Check if M-Pesa settings already exist
        if (MpesaSetting::count() > 0) {
            return; // Don't seed if settings already exist
        }

        MpesaSetting::create([
            'consumer_key' => null,
            'consumer_secret' => null,
            'passkey' => null,
            'shortcode' => null,
            'environment' => 'sandbox',
            'is_enabled' => false,
            'callback_url' => null,
            'confirmation_url' => null,
            'validation_url' => null,
            'min_amount' => 1.00,
            'max_amount' => 70000.00,
            'account_reference' => config('app.name', 'Store'),
            'transaction_desc' => 'Payment for order',
        ]);
    }
}
