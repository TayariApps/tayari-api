<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\{Place,Schedule};
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
        $places = Place::has('schedules')->get();

        if(count($places)){

            

        }

        
    }
}
