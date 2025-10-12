<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\OrganizationProfile;
use App\Helpers\StorageHelper;
use Illuminate\Support\Facades\Storage;

class MigrateOrganizationDocsToGcs extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'migrate:org-docs-to-gcs 
                            {--dry-run : Run without actually migrating files}
                            {--delete-original : Delete original files after successful migration}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Migrate organization registration documents from local/public storage to Google Cloud Storage';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $dryRun = $this->option('dry-run');
        $deleteOriginal = $this->option('delete-original');
        
        if ($dryRun) {
            $this->info('🔍 Running in DRY RUN mode - no files will be migrated');
        }
        
        if ($deleteOriginal && !$dryRun) {
            $this->warn('⚠️  Original files will be DELETED after migration');
            if (!$this->confirm('Are you sure you want to continue?')) {
                $this->info('Migration cancelled.');
                return 0;
            }
        }
        
        // Check if GCS is configured
        if (!config('filesystems.disks.gcs.bucket')) {
            $this->error('❌ GCS is not configured. Please set up GCS in your .env file.');
            return 1;
        }
        
        $this->info('📋 Fetching organization profiles...');
        $profiles = OrganizationProfile::all();
        
        if ($profiles->isEmpty()) {
            $this->info('No organization profiles found.');
            return 0;
        }
        
        $this->info("Found {$profiles->count()} organization profile(s)");
        $this->newLine();
        
        $bar = $this->output->createProgressBar($profiles->count());
        $bar->start();
        
        $stats = [
            'total' => 0,
            'migrated' => 0,
            'already_in_gcs' => 0,
            'not_found' => 0,
            'failed' => 0,
        ];
        
        foreach ($profiles as $profile) {
            $this->newLine();
            $this->info("Processing: {$profile->organization_name} (ID: {$profile->id})");
            
            // Migrate business permit
            if ($profile->business_permit_path) {
                $stats['total']++;
                $result = $this->migrateFile(
                    $profile->business_permit_path,
                    'Business Permit',
                    $dryRun,
                    $deleteOriginal
                );
                $stats[$result]++;
            }
            
            // Migrate government ID
            if ($profile->government_id_path) {
                $stats['total']++;
                $result = $this->migrateFile(
                    $profile->government_id_path,
                    'Government ID',
                    $dryRun,
                    $deleteOriginal
                );
                $stats[$result]++;
            }
            
            // Migrate additional documents
            if ($profile->additional_docs && is_array($profile->additional_docs)) {
                foreach ($profile->additional_docs as $index => $docPath) {
                    $stats['total']++;
                    $result = $this->migrateFile(
                        $docPath,
                        "Additional Doc #" . ($index + 1),
                        $dryRun,
                        $deleteOriginal
                    );
                    $stats[$result]++;
                }
            }
            
            $bar->advance();
        }
        
        $bar->finish();
        $this->newLine(2);
        
        // Display summary
        $this->info('📊 Migration Summary:');
        $this->table(
            ['Status', 'Count'],
            [
                ['Total Files', $stats['total']],
                ['✅ Successfully Migrated', $stats['migrated']],
                ['ℹ️  Already in GCS', $stats['already_in_gcs']],
                ['⚠️  Not Found Locally', $stats['not_found']],
                ['❌ Failed', $stats['failed']],
            ]
        );
        
        if ($dryRun) {
            $this->newLine();
            $this->info('💡 This was a dry run. To actually migrate files, run:');
            $this->line('   php artisan migrate:org-docs-to-gcs');
            $this->newLine();
            $this->info('   To also delete original files after migration, run:');
            $this->line('   php artisan migrate:org-docs-to-gcs --delete-original');
        }
        
        return 0;
    }
    
    /**
     * Migrate a single file
     *
     * @param string $path
     * @param string $label
     * @param bool $dryRun
     * @param bool $deleteOriginal
     * @return string Result status
     */
    protected function migrateFile(string $path, string $label, bool $dryRun, bool $deleteOriginal): string
    {
        // Check if already in GCS
        if (Storage::disk('gcs')->exists($path)) {
            $this->line("  ℹ️  {$label}: Already in GCS");
            return 'already_in_gcs';
        }
        
        // Check in public disk
        $sourceDisk = null;
        if (Storage::disk('public')->exists($path)) {
            $sourceDisk = 'public';
        } elseif (Storage::disk('local')->exists($path)) {
            $sourceDisk = 'local';
        }
        
        if (!$sourceDisk) {
            $this->line("  ⚠️  {$label}: Not found locally ({$path})");
            return 'not_found';
        }
        
        if ($dryRun) {
            $this->line("  🔍 {$label}: Would migrate from '{$sourceDisk}' disk");
            return 'migrated';
        }
        
        // Actually migrate
        $migrated = StorageHelper::migrateToGcs($path, $sourceDisk, $deleteOriginal);
        
        if ($migrated) {
            $action = $deleteOriginal ? 'migrated & deleted' : 'migrated';
            $this->line("  ✅ {$label}: Successfully {$action}");
            return 'migrated';
        } else {
            $this->line("  ❌ {$label}: Migration failed");
            return 'failed';
        }
    }
}
