<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\{Place,Schedule, Day};
use Carbon\Carbon;

class PlaceAvailability extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'place:availability';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check if places are open or closed in a given day and change status in places table';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {

        $dayInWeek = Carbon::now()->format('l');

        $checkIfValuesExist = Day::where('name', $dayInWeek)->has('schedules')->exists();

        if($checkIfValuesExist){
           
            $day = Day::where('name', $dayInWeek)->with('schedules')->first();

            foreach ($day->schedules as $schedule) {
            
                if($schedule->open){
    
                    $place = Place::where('id', $schedule->place_id)->first();
    
                    $place->update([
                        'is_open' => $schedule->open
                    ]);
    
                }
    
            }

        }  

        \Log::info("Place availability cron run at $dayInWeek");
        
    }
}
