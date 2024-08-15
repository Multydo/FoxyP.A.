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
                "error"=> "internal server error (failed to input timezon to session)"
                
            ],500);
        
        }
    }
}