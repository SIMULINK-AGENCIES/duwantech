<?php

namespace App\Console\Commands;

use App\Models\GeneralSetting;
use App\Models\MpesaSetting;
use Illuminate\Console\Command;

class ClearSettingsCache extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'settings:clear-cache {--general : Clear only general settings cache} {--mpesa : Clear only M-Pesa settings cache}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clear all settings cache or specific settings cache';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $general = $this->option('general');
        $mpesa = $this->option('mpesa');

        // If no specific option is provided, clear all
        if (!$general && !$mpesa) {
            $this->clearGeneralCache();
            $this->clearMpesaCache();
            $this->info('All settings cache cleared successfully.');
            return;
        }

        if ($general) {
            $this->clearGeneralCache();
            $this->info('General settings cache cleared successfully.');
        }

        if ($mpesa) {
            $this->clearMpesaCache();
            $this->info('M-Pesa settings cache cleared successfully.');
        }
    }

    /**
     * Clear general settings cache
     */
    private function clearGeneralCache(): void
    {
        try {
            GeneralSetting::clearCache();
            $this->line('✓ General settings cache cleared');
        } catch (\Exception $e) {
            $this->error('Failed to clear general settings cache: ' . $e->getMessage());
        }
    }

    /**
     * Clear M-Pesa settings cache
     */
    private function clearMpesaCache(): void
    {
        try {
            MpesaSetting::clearCache();
            $this->line('✓ M-Pesa settings cache cleared');
        } catch (\Exception $e) {
            $this->error('Failed to clear M-Pesa settings cache: ' . $e->getMessage());
        }
    }
}
