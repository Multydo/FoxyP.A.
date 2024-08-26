<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class timeConv extends Controller
{
    public function timeToSeconds($time) {
    list($hours, $minutes) = explode(':', $time);
    return $hours * 3600 + $minutes * 60;
}

public function secondsToTime($seconds) {
    $hours = floor($seconds / 3600);
    $seconds -= $hours * 3600;
    $minutes = floor($seconds / 60);
    $seconds -= $minutes * 60;
    return sprintf('%02d:%02d:%02d', $hours, $minutes, $seconds);
}
}