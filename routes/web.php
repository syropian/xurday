<?php

use XurDay\Lib\XurClient;
use XurDay\Exceptions\XurNotPresentException;
use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

Route::get('/', function () {
  $inventory = Cache::get('inventory');
  $location = Cache::get('location', 'Location Unknown...');
  $arrival = Carbon::now('America/Los_Angeles')->startOfWeek()->addDays(4)->addHours(1);
  $departure = Carbon::now('America/Los_Angeles')->startOfWeek()->addDays(6)->addHours(1);
  $present = Carbon::now('America/Los_Angeles')->between($arrival, $departure);
  return view('index', [
    'inventory' => $inventory,
    'arrival' => $arrival->toIso8601String(),
    'departure' => $departure->toIso8601String(),
    'present' => (bool)$present,
    'location' => $location
  ]);
});

Route::post('/location', function (Request $request) {
  if (request('token') === config('app.token')) {
    Cache::forget('location');
    $location = request('body');
    Cache::put('location', $location, Carbon::parse('next friday', 'America/Los_Angeles')->addHours(1));

    return response()->json(['status' => 'success'], 200);
  } else {
    return response()->json(['status' => 'forbidden'], 403);
  }
});
