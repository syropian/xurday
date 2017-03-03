<?php

use XurDay\Lib\XurClient;
use XurDay\Exceptions\XurNotPresentException;
use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

Route::get('/', function () {
  $inventory = Cache::get('inventory');
  $arrival = Carbon::now('America/Los_Angeles')->startOfWeek()->addDays(4)->addHours(1);
  $departure = Carbon::now('America/Los_Angeles')->startOfWeek()->addDays(6)->addHours(1);
  $present = Carbon::now('America/Los_Angeles')->between($arrival, $departure);

  return view('index', [
    'inventory' => $inventory,
    'arrival' => $arrival->toIso8601String(),
    'departure' => $departure->toIso8601String(),
    'present' => (bool)$present
  ]);
});
