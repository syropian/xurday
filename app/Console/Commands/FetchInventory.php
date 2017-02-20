<?php

namespace XurDay\Console\Commands;

use Illuminate\Console\Command;
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
      $key = config('services.bungie.key');
      $client = new XurClient($key);
      try {
        $client->getInventory();
        $this->info('Inventory successfully cached!');
      } catch(\Exception $e) {
        $this->error('Unable to fetch inventory.');
      }

    }
}
