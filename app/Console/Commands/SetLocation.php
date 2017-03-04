<?php

namespace XurDay\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;

class SetLocation extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'location:set';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Manually sets Xur\'s location.' ;

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
     * @return mixed
     */
    public function handle()
    {
      Cache::forget('location');
      $location = $this->ask('Where is Xur this week?');
      Cache::put('location', $location, Carbon::parse('next friday', 'America/Los_Angeles')->addHours(1));
    }
}
