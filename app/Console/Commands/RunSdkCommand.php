<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class RunSdkCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sdk:run';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Run the SDK command';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {

        try {
            exec('cd ../sdk && run.bat');
        } catch (\Throwable $th) {
            echo "[" . date("Y-m-d H:i:s") . "] Cron: RestartSdk. Error occurred while inserting logs.\n";
        }
    }
}
