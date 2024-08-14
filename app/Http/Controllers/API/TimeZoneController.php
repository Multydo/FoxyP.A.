<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Controllers\API\Session_controler;
use App\Http\Controllers\API\SetUserTables;

class TimeZoneController extends Controller
{
    public function getTimeZone(Request $request){
       $userToken = $request->bearerToken();
        $timezoneOffset = $request->input('timezone');
        $data = [
            "key" => "time_zone",
            "data" => "$timezoneOffset"
        ];
        $session_req = new Session_controler;
        $session_result = $session_req -> session_selector("put" , $data);

        if ($session_result){
            $userTable = new SetUserTables;
            $userTable->fillSettings($timezoneOffset,$request);

        }else{
            return response()->json([
                "error"=> "failed to input timezon to session"
                ,400
            ]);
        
        }
    }
}