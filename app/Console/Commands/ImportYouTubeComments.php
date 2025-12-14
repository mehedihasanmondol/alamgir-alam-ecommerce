<?php

namespace App\Console\Commands;

use App\Services\YouTubeCommentService;
use Illuminate\Console\Command;

/**
 * Import YouTube Comments Command
 * 
 * Artisan command to import YouTube comments as feedback
 * Can be run manually or via Laravel scheduler
 * 
 * @category Console Commands
 * @package  App\Console\Commands
 * @created  2025-12-14
 */
class ImportYouTubeComments extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'youtube:import-comments {--force : Force import even if disabled}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import YouTube comments from channel videos as project feedback';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('Starting YouTube comment import...');
        $this->newLine();

        $force = $this->option('force');

        if ($force) {
            $this->warn('Force mode enabled - bypassing import enabled check');
        }

        $youtubeService = app(YouTubeCommentService::class);

        // Test connection first
        $this->info('Testing YouTube API connection...');
        $connectionTest = $youtubeService->testConnection();

        if (!$connectionTest['success']) {
            $this->error('Connection failed: ' . $connectionTest['message']);
            return Command::FAILURE;
        }

        $this->info('✓ ' . $connectionTest['message']);
        $this->newLine();

        // Start import
        $this->info('Fetching and importing comments...');
        $progressBar = $this->output->createProgressBar();
        $progressBar->start();

        $result = $youtubeService->syncComments();

        $progressBar->finish();
        $this->newLine(2);

        if (!$result['success'] && !$force) {
            $this->error($result['message']);
            return Command::FAILURE;
        }

        // Display results
        $this->info('Import completed!');
        $this->newLine();

        $this->table(
            ['Metric', 'Count'],
            [
                ['Imported', $result['imported'] ?? 0],
                ['Skipped (Duplicates)', $result['skipped'] ?? 0],
                ['Errors', $result['errors'] ?? 0],
            ]
        );

        $this->newLine();
        $this->info('✓ ' . $result['message']);

        return Command::SUCCESS;
    }
}
