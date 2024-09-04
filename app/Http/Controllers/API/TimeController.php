<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Http\Controllers\API\SessionController;
use App\Http\Controllers\API\DynamicTableController;
use App\http\Controllers\API\UserController;

class TimeController extends Controller
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
public function getTimeZone(Request $request){
       $userToken = $request->bearerToken();
       
        $timezoneOffset = $request->input('timezone');
        $data = [
            "key" => "time_zone",
            "data" => "$timezoneOffset"
        ];
        $session_req = new SessionController;
        $session_result = $session_req -> session_selector("put" , $data);

        if ($session_result){
            $userTable = new DynamicTableController;
            $status = $userTable->fillSettings($timezoneOffset,$request);
            if($status){
                return response()->json([
                    "massage"=>"setting data saved"
                ],200);
            }else{
                 return response()->json([
                    "massage"=>"internal serer error (settings data not saved)"
                ],500);
            }

        }else{
            return response()->json([
                "message"=> "internal server error (failed to input timezon to session)"
                
            ],500);
        
        }
    }
}