<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;

class GMTConverter extends Controller
{
    public function convertFromGMT($gmtTime, $timeZoneOffset) {
        $dateTime = Carbon::createFromFormat('H:i:s', $gmtTime, 'GMT');
    
    
        $dateTime->addHours($timeZoneOffset);

        return $dateTime->format('H:i:s');
    }

    public function convertToGMT($localTime, $timeZoneOffset) {
        $dateTime = Carbon::createFromFormat('H:i:s', $localTime);

    
        $dateTime->subHours($timeZoneOffset);

        return $dateTime->format('H:i:s');
    }
}