<?php

namespace XurDay\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use XurDay\Lib\XurClient;

class FetchInventory extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'inventory:fetch';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fetches Xur\'s inventory for the week.';

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
      $this->info('Fetching inventory...');
      $key = config('services.bungie.key');
      $client = new XurClient($key);
      try {
        $client->getInventory();
        $this->info('Inventory successfully fetched & cached!');
      } catch(\Exception $e) {
        Log::error($e);
        $this->error('Unable to fetch inventory. Check the logs for more information.');
      }

    }
}
