<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Events\MedideNotification;

class BroadcastMsg extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:broadcast-msg';

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
        //
        $this->line('Starting to take backup...');
        broadcast(new MedideNotification('Test Message', 'jobrequest'))->toOthers();
    }
}
