<?php

namespace App\Console\Commands;

use App\Services\UserActivityService;
use Illuminate\Console\Command;

class CleanupOldSessions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sessions:cleanup 
                           {--minutes=30 : Number of minutes to consider a session inactive}
                           {--dry-run : Show what would be cleaned without actually doing it}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clean up old inactive user sessions and broadcast offline events';

    protected $userActivityService;

    /**
     * Create a new command instance.
     */
    public function __construct(UserActivityService $userActivityService)
    {
        parent::__construct();
        $this->userActivityService = $userActivityService;
    }

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('Starting session cleanup...');

        $dryRun = $this->option('dry-run');
        $minutes = (int) $this->option('minutes');

        if ($dryRun) {
            $this->warn('DRY RUN MODE - No sessions will actually be deleted');
        }

        try {
            // Get sessions that would be cleaned
            $cutoffTime = now()->subMinutes($minutes);
            $oldSessionsQuery = \App\Models\ActiveSession::where('last_activity', '<', $cutoffTime);
            
            $sessionCount = $oldSessionsQuery->count();
            
            if ($sessionCount === 0) {
                $this->info('No inactive sessions found.');
                return self::SUCCESS;
            }

            $this->info("Found {$sessionCount} inactive sessions (older than {$minutes} minutes)");

            if ($dryRun) {
                // Show what would be cleaned
                $sessions = $oldSessionsQuery->with('user')->get();
                
                $this->table(
                    ['Session ID', 'User', 'Last Activity', 'Duration'],
                    $sessions->map(function ($session) {
                        return [
                            substr($session->session_id, 0, 12) . '...',
                            $session->user ? $session->user->name : 'Guest',
                            $session->last_activity->diffForHumans(),
                            $session->created_at->diffForHumans($session->last_activity, true),
                        ];
                    })->toArray()
                );

                $this->info('Run without --dry-run to actually clean up these sessions.');
                return self::SUCCESS;
            }

            // Perform actual cleanup
            if ($this->confirm("Are you sure you want to clean up {$sessionCount} inactive sessions?")) {
                $cleanedCount = $this->userActivityService->cleanupOldSessions();
                $this->info("Successfully cleaned up {$cleanedCount} inactive sessions.");
                
                // Show current stats
                $this->displayCurrentStats();
            } else {
                $this->info('Cleanup cancelled.');
            }

        } catch (\Exception $e) {
            $this->error('Error during session cleanup: ' . $e->getMessage());
            return self::FAILURE;
        }

        return self::SUCCESS;
    }

    /**
     * Display current activity statistics.
     */
    protected function displayCurrentStats(): void
    {
        try {
            $stats = $this->userActivityService->getActivityStats();
            
            $this->newLine();
            $this->info('Current Activity Statistics:');
            $this->line("Active Users: {$stats['total_active_users']}");
            $this->line("Authenticated: {$stats['authenticated_users']}");
            $this->line("Guests: {$stats['guest_users']}");
            $this->line("Sessions Today: {$stats['sessions_today']}");
            
            if (!empty($stats['top_countries'])) {
                $this->newLine();
                $this->info('Top Countries:');
                foreach ($stats['top_countries'] as $country) {
                    $this->line("  {$country['country']}: {$country['count']} sessions");
                }
            }
            
        } catch (\Exception $e) {
            $this->warn('Could not fetch current stats: ' . $e->getMessage());
        }
    }
}
