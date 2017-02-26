<?php

use XurDay\Lib\XurClient;
use XurDay\Exceptions\XurNotPresentException;
use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

Route::get('/', function () {
  $inventory = Cache::get('inventory');

  $departure = Carbon::parse('this sunday', 'America/Los_Angeles')->addHours(1);
  $arrival = Carbon::parse('this friday', 'America/Los_Angeles')->addHours(1);
  $present = $departure->lt($arrival) && Carbon::now('America/Los_Angeles')->lt($departure);

  return view('index', [
    'inventory' => $inventory,
    'arrival' => $arrival->toIso8601String(),
    'departure' => $departure->toIso8601String(),
    'present' => (bool)$present
  ]);
});
