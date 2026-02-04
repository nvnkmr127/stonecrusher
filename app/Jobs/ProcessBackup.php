<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;

class ProcessBackup implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * The task type (full or db-only).
     */
    protected string $type;

    /**
     * Create a new job instance.
     */
    public function __construct(string $type = 'full')
    {
        $this->type = $type;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        Log::info("Backup job started. Type: {$this->type}");

        try {
            if ($this->type === 'db-only') {
                Artisan::call('backup:run --only-db');
            } else {
                Artisan::call('backup:run');
            }
            Log::info("Backup job finished successfully.");
        } catch (\Exception $e) {
            Log::error("Backup job failed: " . $e->getMessage());
            throw $e;
        }
    }
}
