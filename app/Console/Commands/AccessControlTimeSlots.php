<?php

namespace App\Console\Commands;

use App\Http\Controllers\SDKController;
use App\Models\AccessControlTimeSlot;
use Illuminate\Console\Command;

class AccessControlTimeSlots extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'task:AccessControlTimeSlots {device_id} {sdkCommand}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'AccessControlTimeSlots device_id command';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $id = $this->argument("device_id");
        $command = $this->argument("sdkCommand");


        $result = (new SDKController)->handleCommand($id, $command);

        return $this->info($result);
    }
}
