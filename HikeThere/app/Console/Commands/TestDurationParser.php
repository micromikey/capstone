<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\DurationParserService;

class TestDurationParser extends Command
{
    protected $signature = 'test:duration-parser';
    protected $description = 'Test duration parser service';

    protected $durationParser;

    public function __construct(DurationParserService $durationParser)
    {
        parent::__construct();
        $this->durationParser = $durationParser;
    }

    public function handle()
    {
        $this->info('🔍 DURATION PARSER TEST');
        $this->info('======================');
        $this->newLine();

        $testDurations = [
            '36 hours',
            '2 days',
            '48 hours',
            '1 day 1 night',
            '3 days 2 nights'
        ];

        foreach ($testDurations as $duration) {
            $this->info("Testing: '{$duration}'");
            try {
                $result = $this->durationParser->normalizeDuration($duration);
                if ($result) {
                    $this->info("  ✓ Result: {$result['days']} days, {$result['nights']} nights");
                } else {
                    $this->error("  ✗ No result returned");
                }
            } catch (\Exception $e) {
                $this->error("  ✗ Error: " . $e->getMessage());
            }
            $this->newLine();
        }

        $this->info('✨ Test complete!');
    }
}