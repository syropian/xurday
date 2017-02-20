<?php

use XurDay\Lib\XurClient;
use XurDay\Exceptions\XurNotPresentException;
use Illuminate\Support\Facades\Cache;

Route::get('/', function () {
  if (Cache::has('inventory')) {
    dd(Cache::get('inventory'));
  } else {
    dd('No inventory present');
  }
});
