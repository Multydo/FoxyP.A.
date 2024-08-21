<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Controllers\API\getUserId;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class homePage extends Controller
{
    public function master(Request $request){
        
        $get_user_id = new getUserId;
        $userId = $get_user_id -> getId($request);

        $user_table = $userId."_app";
        $todaydate =Carbon::now()->format('Y-m-d');
        $dates =DB::table($user_table)
                ->selectRaw('DISTINCT DATE(time_from) as date_part')
                ->where('time_from','>=',$todaydate)
                ->pluck('date_part')
                ->toArray();
        if($dates){
            return response()->json([
                "message" => "appointments found for user",
                "status" => true,
                "data" => $dates
            ],200);
        }else{
            return response()->json([
                "message" => "no appointments found for user",
                "status" => false,
                "data" => ""
            ],200);
        }
        
    }
}