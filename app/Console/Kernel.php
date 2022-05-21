<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Laravel\Lumen\Console\Kernel as ConsoleKernel;
use App\FurnitureFetchApi\LaunchScrappingScript;
use Log;
use App\FurniturePlatform;
class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        //
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        //

        $schedule->call(function () {
            try{
                // Launch script
                new LaunchScrappingScript();
            }catch(\Exception $e){
                Log::error("Error Scripting Scheduling : ".$e->getMessage());
            }
        })->after(function () {
            try{
                // Task is complete...
                FurniturePlatform::where(["fetched_status" => 1])->update(['fetched_status' => 0]);
            }catch(\Exception $e){
                Log::error("Error in reset platforms to zero : ".$e->getMessage());
            }
        })->everyMinute();
    }
}
