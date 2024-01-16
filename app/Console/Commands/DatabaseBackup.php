<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class DatabaseBackup extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'db:backup';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->line('Starting to take backup...');
        if (! Storage::exists('backup')) {
            Storage::makeDirectory('backup');
            $this->line('Created Backup Directory');
        }

        $filename = "backup-" . Carbon::now()->format('Y-m-d') . ".gz";

        $command = env('DB_DUMP_PATH') . "mysqldump -u " . env('DB_DUMP_USERNAME') . " -p'" . env('DB_DUMP_PASSWORD')
                . "' " . env('DB_DATABASE')
                . "  | gzip > " . storage_path() . "/app/backup/" . $filename;

        $returnVar = null;
        $output  = null;
        $this->line($command);
        exec($command, $output, $returnVar);
        $this->line(storage_path() . "/app/backup/" . $filename);
    }
}
