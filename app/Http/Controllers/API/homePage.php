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

    public function showDetails(Request $request)
{
    $sessionCon = new Session_controler;
    $gmtCon = new GMTConverter;
    $get_user_id = new getUserId;
    $token = $request->bearerToken();
   
    $user_id = $get_user_id->getId($token);
    $r_date = $request->input('r_date');
    $user_a_table = $user_id . "_app";

    $today = Carbon::now()->format('Y-m-d');

    if ($r_date >= $today) {

        $min_date = $r_date . " 00:00:00";
        $max_date = $r_date . " 23:59:59";

        $appointments = DB::table($user_a_table)
            ->where('owner_id', $user_id)
            ->where('time_from', '>', $min_date)
            ->where('time_to', '<', $max_date)
            ->get(['id', 'app_id', 'app_user_id', 'time_from', 'time_to', 'title', 'description']);

        if ($appointments->count() > 0) {
            $time_data = [];
            foreach ($appointments as $index => $row) {
                $date_from = new Carbon($row->time_from);
                $temp_d_f = $gmtCon->convertFromGMT($date_from->format('H:i:s'), $sessionCon->session_selector("get", ["key" => "timezone", "data" => ""]));

                $date_to = new Carbon($row->time_to);
                $temp_d_t = $gmtCon->convertFromGMT($date_to->format('H:i:s'), $sessionCon->session_selector("get", ["key" => "timezone", "data" => ""]));

                $time_data[$index]['start'] = $temp_d_f;
                $time_data[$index]['end'] = $temp_d_t;
                $time_data[$index]['app_id'] = $row->app_id;
                $time_data[$index]['title'] = $row->title;
                $time_data[$index]['desc'] = $row->description;
                $time_data[$index]['aid'] = $row->id;

                $user_p_id = $row->app_user_id;

                if ($user_p_id != "xxxx") {
                    $user_info = DB::table('users')
                        ->where('id', $user_p_id)
                        ->select('id', 'fname', 'lname')
                        ->first();

                    $time_data[$index]['id'] = $user_info->id;
                    $time_data[$index]['fname'] = $user_info->fname;
                    $time_data[$index]['lname'] = $user_info->lname;
                } else {
                    $time_data[$index]['id'] = "-";
                    $time_data[$index]['fname'] = "-";
                    $time_data[$index]['lname'] = "-";
                }
            }

            $data = [
                "key" => "time_a_data",
                "data" => $time_data
            ];
            $sessionCon->session_selector("put", $data);

            return response()->json([
                "time_data" => $time_data
            ]);
        } else {
            return redirect('main');
        }
    } else {
        return redirect('main');
    }
}

}