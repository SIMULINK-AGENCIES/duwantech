<?php

namespace App\Console\Commands;

use App\Services\MailService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use App\Models\User;

class TestMailConfiguration extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'mail:test {--send : Actually send test emails}';

    /**
     * The console command description.
     */
    protected $description = 'Test mail configuration and templates';

    /**
     * The mail service instance.
     */
    protected MailService $mailService;

    /**
     * Create a new command instance.
     */
    public function __construct(MailService $mailService)
    {
        parent::__construct();
        $this->mailService = $mailService;
    }

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('🔧 Testing Mail Configuration...');
        $this->newLine();

        // Test configuration
        $this->testConfiguration();
        
        // Test templates
        $this->testTemplates();
        
        // Test queue system
        $this->testQueueSystem();
        
        // Send test emails if requested
        if ($this->option('send')) {
            $this->sendTestEmails();
        }

        $this->newLine();
        $this->info('✅ Mail configuration test completed!');
        
        return Command::SUCCESS;
    }

    /**
     * Test basic configuration.
     */
    protected function testConfiguration(): void
    {
        $this->info('1. Testing Basic Configuration');
        
        $config = [
            'MAIL_MAILER' => config('mail.default'),
            'MAIL_HOST' => config('mail.mailers.smtp.host'),
            'MAIL_PORT' => config('mail.mailers.smtp.port'),
            'MAIL_USERNAME' => config('mail.mailers.smtp.username'),
            'MAIL_ENCRYPTION' => config('mail.mailers.smtp.encryption'),
            'MAIL_FROM_ADDRESS' => config('mail.from.address'),
            'MAIL_FROM_NAME' => config('mail.from.name'),
        ];

        foreach ($config as $key => $value) {
            $status = $value ? '✅' : '❌';
            $this->line("   {$status} {$key}: " . ($value ?: 'Not set'));
        }
        
        $this->newLine();
    }

    /**
     * Test email templates.
     */
    protected function testTemplates(): void
    {
        $this->info('2. Testing Email Templates');
        
        $templates = [
            'emails.layouts.base' => 'Base Layout',
            'emails.orders.confirmation' => 'Order Confirmation',
            'emails.orders.status-update' => 'Order Status Update',
            'emails.payments.confirmation' => 'Payment Confirmation',
            'emails.payments.failed' => 'Payment Failed',
            'emails.system.stock-alert' => 'Stock Alert',
            'emails.users.welcome' => 'Welcome User',
            'emails.system.admin-notification' => 'Admin Notification',
            'emails.system.alert' => 'System Alert',
        ];

        foreach ($templates as $template => $name) {
            try {
                $testData = $this->getTestData($template);
                view($template, $testData);
                $this->line("   ✅ {$name}");
            } catch (\Exception $e) {
                $this->line("   ❌ {$name}: " . $e->getMessage());
            }
        }
        
        $this->newLine();
    }

    /**
     * Test queue system.
     */
    protected function testQueueSystem(): void
    {
        $this->info('3. Testing Queue System');
        
        $queueConfig = [
            'Default Connection' => config('queue.default'),
            'Queue Driver' => config('queue.connections.' . config('queue.default') . '.driver'),
            'Queue Table' => config('queue.connections.database.table', 'jobs'),
        ];

        foreach ($queueConfig as $key => $value) {
            $this->line("   ✅ {$key}: {$value}");
        }
        
        // Test queue status
        $status = $this->mailService->getQueueStatus();
        if (!empty($status)) {
            $this->line("   ✅ Queue Status: Retrieved successfully");
        } else {
            $this->line("   ⚠️  Queue Status: Could not retrieve status");
        }
        
        $this->newLine();
    }

    /**
     * Send test emails.
     */
    protected function sendTestEmails(): void
    {
        $this->info('4. Sending Test Emails');
        
        if (!$this->confirm('This will send actual test emails. Continue?')) {
            $this->line('   ⏭️  Skipped sending test emails');
            return;
        }

        $testEmail = $this->ask('Enter test email address', config('mail.from.address'));
        
        if (!filter_var($testEmail, FILTER_VALIDATE_EMAIL)) {
            $this->error('   ❌ Invalid email address');
            return;
        }

        // Create a test user
        $testUser = new User([
            'name' => 'Test User',
            'email' => $testEmail,
        ]);

        $tests = [
            'Welcome Email' => function() use ($testUser) {
                return $this->mailService->sendWelcomeEmail($testUser);
            },
            'System Alert' => function() use ($testUser) {
                return $this->mailService->sendSystemAlert(
                    'test_alert',
                    'Test System Alert',
                    'This is a test system alert to verify email functionality.',
                    ['test' => true],
                    'low',
                    $testUser
                );
            },
        ];

        foreach ($tests as $testName => $testFunction) {
            try {
                if ($testFunction()) {
                    $this->line("   ✅ {$testName}: Sent successfully");
                } else {
                    $this->line("   ❌ {$testName}: Failed to send");
                }
            } catch (\Exception $e) {
                $this->line("   ❌ {$testName}: " . $e->getMessage());
            }
        }
        
        $this->newLine();
        $this->info("📧 Test emails sent to: {$testEmail}");
    }

    /**
     * Get test data for templates.
     */
    protected function getTestData(string $template): array
    {
        $baseData = [
            'storeName' => config('app.name'),
            'storeUrl' => config('app.url'),
            'supportEmail' => config('mail.from.address'),
            'supportPhone' => '+254 700 000 000',
            'supportUrl' => '#',
        ];

        return match($template) {
            'emails.orders.confirmation' => array_merge($baseData, [
                'customerName' => 'Test Customer',
                'orderNumber' => 'TEST-001',
                'orderDate' => now(),
                'paymentMethod' => 'Test Payment',
                'orderAmount' => 1000.00,
                'productName' => 'Test Product',
                'productImage' => null,
                'quantity' => 1,
                'unitPrice' => 1000.00,
                'trackingUrl' => '#',
            ]),
            'emails.orders.status-update' => array_merge($baseData, [
                'customerName' => 'Test Customer',
                'orderNumber' => 'TEST-001',
                'oldStatus' => 'pending',
                'newStatus' => 'confirmed',
                'statusTitle' => 'Order Confirmed',
                'statusMessage' => 'Your order has been confirmed',
                'statusColor' => '#10b981',
                'productName' => 'Test Product',
                'orderAmount' => 1000.00,
                'orderDate' => now(),
                'trackingUrl' => '#',
                'timeline' => [],
                'nextSteps' => ['Your order will be processed'],
            ]),
            'emails.users.welcome' => array_merge($baseData, [
                'userName' => 'Test User',
                'profileUrl' => '#',
                'shopUrl' => '#',
                'popularCategories' => [
                    ['name' => 'Test Category', 'icon' => '📦', 'url' => '#', 'count' => 10],
                ],
            ]),
            default => $baseData,
        };
    }
}
