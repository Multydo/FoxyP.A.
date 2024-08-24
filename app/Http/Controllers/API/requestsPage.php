<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Controllers\API\Session_controler;
use App\http\Controllers\API\GMTConverter;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\API\getUserId;
use Throwable;
use App\Models\setting;
use Carbon\Carbon;

class requestsPage extends Controller
{
    public function setRequest(Request $request){
        $user_p_info = [];
        $token = $request -> bearerToken();
        $getUserId = new getUserId;
        $sessionCon = new Session_controler;
        $gmtcon =new GMTConverter;
        $user_id = $getUserId -> getId($token);
        if($request->has("other_user")){
            $user_f_id = $request["other_user"];
            try{
                $data=[
                    "key"=>"user_p_id",
                    "data"=>$user_f_id
                ];
               $saveId = $sessionCon->session_selector("put",$data);
            }catch(Throwable $e){
                return response()->json([
                    "message"=>"everbebert"
                ],500);
            }
        }else{
            $data=[
                "key"=>"user_p_id",
                "data"=>""
            ];
            $user_f_id = $sessionCon->session_selector("get",$data);
        }

        $user_p_info["user_id"] = $user_f_id;

        $user_a_table = $user_id."_app";
        $user_f_a_table = $user_f_id . "_app";

        $max_app_f = setting::where("owner_id",$user_f_id)->value("max_app");
        $max_app = setting::where("owner_id" ,$user_id)->value("max_app");
        
        $user_p_info["max_app"] = $max_app_f;

        $full_days = DB::table($user_f_a_table)
        ->select(DB::raw('DATE(time_from) AS date_part, COUNT(*) AS count'))
        ->where('app_user_id', '<>', 'xxxx')
        ->groupBy('date_part')
        ->having('count', '>=', $max_app_f)
        ->pluck('date_part')
        ->toArray();

        $user_p_info['full_days'] = $full_days;

        $info = DB::table('users as u')
        ->join("settings as s ", 'u.id', '=', 's.owner_id')
        ->where('s.owner_id', $user_f_id )
        ->select('u.fname', 'u.lname', 's.work_from', 's.work_to', 's.time_zone')
        ->first();
        $data = [
            "key"=>"time_zone",
            "data"=>""
        ];

        $timeZone_data = $sessionCon->session_selector("get",$data);
        $timezone = $timeZone_data["data"];

        $info->work_from = $gmtcon->convertFromGMT($info->work_from , $timezone);
        $info->work_to = $gmtcon->convertFromGMT($info->work_to , $timezone);

         $user_p_info['info'] = $info;

        $info_day = setting::where("owner_id", $user_f_id)
        ->select("monday","tuesday","wednesday","thursday","friday","saturday","sunday")
        ->first()
        ->toArray();

        $user_p_info["info_day"]= $info_day;

        $data=[
            "key"=>"user_p_info",
            "data"=>$user_p_info
        ];

        $save_user_p = $sessionCon->session_selector("put",$data);

        return response()->json([
            "info"=>$info,
            "full_days"=>$full_days,
            "info_day"=>$info_day
        ]);
    }

    public function checkDateAvailability(Request $request)
{
    //$this->setRequest($request);
    $data = [
        "key" => "user_p_info",
        "data" => ""
    ];
    $gmtCon = new GMTConverter;
    $SessionCon = new Session_controler;
    $user_p_info = $SessionCon->session_selector("get", $data);
    $info = [$user_p_info['info']];
    $full_days = $user_p_info['full_days']; // Corrected 'full_day' to 'full_days'
    $info_day = [$user_p_info['info_day']];

    $user_p_id = $user_p_info['user_id'];
    $r_date = $request->r_date;
    $p_full_day = $user_p_info['full_days']; // Corrected 'full_day' to 'full_days'
    $today = Carbon::now()->format('Y-m-d');

    if ($r_date >= $today) {
        if (empty($p_full_day)) {
            $temp = $this->checkWorkDay($r_date, $p_full_day);
            if ($temp) {
                $user_p_a_table = $user_p_id . "_app";
                $temp_r_date_1 = $r_date . " 00:00:00";
                $temp_r_date_2 = $r_date . " 23:59:59";

                $appointments = DB::table($user_p_a_table)
                    ->where('time_from', '>=', $temp_r_date_1)
                    ->where('time_to', '<=', $temp_r_date_2)
                    ->get(['time_from', 'time_to']);

                $time_data = [];
                $data = [
                    "key" => "timezone",
                    "data" => ""
                ];
                $timezone = $SessionCon->session_selector("get", $data);

                foreach ($appointments as $index => $row) {
                    $time_data[$index]['start'] = $gmtCon->convertFromGMT(Carbon::parse($row->time_from)->format('H:i:s'), $timezone);
                    $time_data[$index]['end'] = $gmtCon->convertFromGMT(Carbon::parse($row->time_to)->format('H:i:s'), $timezone);
                }

                $work_data = [
                    ['start' => $user_p_info['info']['work_from'], 'end' => $user_p_info['info']['work_to']]
                ];

                return response()->json([
                    'r_date' => $r_date,
                    'time_data' => $time_data,
                    'work_data' => $work_data
                ], 200);
            } else {
                return response()->json([
                    'message' => 'The requested date (' . $r_date . ') for that person is full/closed. Please select a date that might not be full/closed.'
                ], 409);
            }
        } else {
            if (!in_array($r_date, $p_full_day)) {
                if ($this->checkWorkDay($r_date, $p_full_day)) {
                    $user_p_a_table = $user_p_id . "_app";
                    $temp_r_date_1 = $r_date . " 00:00:00";
                    $temp_r_date_2 = $r_date . " 23:59:59";

                    $appointments = DB::table($user_p_a_table)
                        ->where('time_from', '>=', $temp_r_date_1)
                        ->where('time_to', '<=', $temp_r_date_2)
                        ->get(['time_from', 'time_to']);
                    $timezone = $SessionCon->session_selector("get", $data);
                    $time_data = [];
                    foreach ($appointments as $index => $row) {
                        $time_data[$index]['start'] = $gmtCon->convertFromGMT(Carbon::parse($row->time_from)->format('H:i:s'), $timezone);
                        $time_data[$index]['end'] = $gmtCon->convertFromGMT(Carbon::parse($row->time_to)->format('H:i:s'), $timezone);
                    }

                    $work_data = [
                        ['start' => $user_p_info['info']['work_from'], 'end' => $user_p_info['info']['work_to']]
                    ];

                    return response()->json([
                        'r_date' => $r_date,
                        'time_data' => $time_data,
                        'work_data' => $work_data
                    ], 200);
                } else {
                    return response()->json([
                        'message' => 'The requested date (' . $r_date . ') for that person is full/closed. Please select a date that might not be full/closed.'
                    ], 409);
                }
            } else {
                return response()->json([
                    'message' => 'The requested date (' . $r_date . ') for that person is full/closed. Please select a date that might not be full/closed.'
                ], 409);
            }
        }
    } else {
        return response()->json([
            'message' => 'The requested date (' . $r_date . ') is in the past. Please select a suitable date.'
        ], 400);
    }
}

private function check_work_d($r_date,$p_full_day){
    $data= [
        "key" =>"user_p_info",
        "data"=>""
    ];
    $sessionCon = new Session_controler;
    $user_p_info = $sessionCon -> session_selector("get",$data);
    $day_num = Carbon::parse($r_date)->dayOfWeek;
    $days_list = [
        $user_p_info['info_day']['sunday'],
        $user_p_info['info_day']['monday'],
        $user_p_info['info_day']['tuesday'],
        $user_p_info['info_day']['wednesday'],
        $user_p_info['info_day']['thursday'],
        $user_p_info['info_day']['friday'],
        $user_p_info['info_day']['saturday'],
    ];

    if (empty($p_full_day)) {
        return $days_list[$day_num] === 'accepted';
    } else {
        if (in_array($r_date, $p_full_day)) {
            return false;
        } else {
            return $days_list[$day_num] === 'accepted';
        }
    }
}

public function sendRequest(Request $request){
    $data=[
        "key"=>"user_p_info",
        "data"=>""
    ];
    $sessionCon = new Session_controler;
    $gmtCon = new GMTConverter;
    $user_p_info = $sessionCon -> session_selector("get",$data);
    $r_date = $request ->input("r_date");
     $starting_t_x = $request->input('starting_t');
    $ending_t_x = $request->input('ending_t');
    $r_title = $request->input('r_title');
    $r_desc = $request->input('r_desc');

    $data=[
        "key"=>"timezone",
        "data"=>""
    ];

    $timezone = $sessionCon ->session_selector("get",$data);


    $starting_t = $gmtCon->convertToGMT($starting_t_x,$timezone);
    $ending_t = $gmtCon -> convertToGMT($ending_t_x, $timezone);

    $token = $request -> bearerToken();
    $getId = new getUserId;
    $user_id = $getId -> getId($token);

    $user_p_id = $user_p_info["user_id"];

    $user_p_a_table = $user_p_id ."_app";
    $user_a_table = $user_id ."_app";
    $break_time_p = setting::where("owner_id", $user_p_id)->value("break_time");

    $total_time = timeToSeconds($ending_t) + timeToSeconds($break_time_p);
    $final_e_t = secondsToTime($total_time);

    $r_start = $r_date . " " . $starting_t;
    $r_end = $r_date . " " . $ending_t;
    $r_b_end = $r_date . " " . $final_e_t;

    $temp_r_date_1 = $r_date . " 00:00:00";
    $temp_r_date_2 = $r_date . " 23:59:59"; 
    $current_time = Carbon::now()->format('H:i:s');
    $today = Carbon::now()->format('Y-m-d');

}

}