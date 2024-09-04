<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Controllers\API\SessionController;
use App\HTTP\Controllers\API\TimeController;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\API\UserController;
use Throwable;
use App\Models\setting;
use Carbon\Carbon;

class RequestController extends Controller
{
     public function setRequest(Request $request){
        $user_p_info = [];
        $token = $request -> bearerToken();
        $UserController = new UserController;
        $sessionCon = new SessionController;
        $gmtcon =new TimeController;
        $user_id = $UserController -> getId($token);
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

        $info = DB::table('settings')
     ->where('owner_id', $user_f_id)
     ->select('work_from', 'work_to', 'time_zone')
     ->first();
     //dd($info);
        $data = [
            "key"=>"time_zone",
            "data"=>""
        ];

        $timeZone_data = $sessionCon->session_selector("get",$data);
        //dd($timeZone_data);
        $timezone = $timeZone_data;

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

    public function checkDateAvailability(Request $request){
    //$this->setRequest($request);
    $data = [
        "key" => "user_p_info",
        "data" => ""
    ];
    $gmtCon = new TimeController;
    $SessionCon = new SessionController;
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
                    ['start' => $user_p_info['info']->work_from, 'end' => $user_p_info['info']->work_to]
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
    $sessionCon = new SessionController;
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
    $data = [
        "key" => "user_p_info",
        "data" => ""
    ];
    $sessionCon = new SessionController;
    $gmtCon = new TimeController;
    $user_p_info = $sessionCon->session_selector("get", $data);
    $r_date = $request->input("r_date");
    $starting_t_x = $request->input('starting_t');
    $ending_t_x = $request->input('ending_t');
    $r_title = $request->input('r_title');
    $r_desc = $request->input('r_desc');

    $data = [
        "key" => "timezone",
        "data" => ""
    ];

    $timezone = $sessionCon->session_selector("get", $data);

    $starting_t = $gmtCon->convertToGMT($starting_t_x, $timezone);
    $ending_t = $gmtCon->convertToGMT($ending_t_x, $timezone);

    $token = $request->bearerToken();
    $getId = new UserController;
    $user_id = $getId->getId($token);

    $user_p_id = $user_p_info["user_id"];

    $user_p_a_table = $user_p_id . "_app";
    $user_a_table = $user_id . "_app";
    $break_time_p = setting::where("owner_id", $user_p_id)->value("break_time");

    $timeConv = new TimeController;

    $total_time = $timeConv->timeToSeconds($ending_t) + $timeConv->timeToSeconds($break_time_p);
    $final_e_t = $timeConv->secondsToTime($total_time);

    $r_start = $r_date . " " . $starting_t;
    $r_end = $r_date . " " . $ending_t;
    $r_b_end = $r_date . " " . $final_e_t;

    $temp_r_date_1 = $r_date . " 00:00:00";
    $temp_r_date_2 = $r_date . " 23:59:59"; 
    $current_time = Carbon::now()->format('H:i:s');
    $today = Carbon::now()->format('Y-m-d');

    if ($r_date == $today && $starting_t_x >= $current_time) {
        if ($starting_t < $ending_t) {
            $time_data = DB::table($user_p_a_table)
                ->whereBetween("time_from", [$temp_r_date_1, $temp_r_date_2])
                ->get(["time_from", "time_to"])
                ->map(function($row) {
                    return ["start" => $row->time_from, "end" => $row->time_to];
                })->toArray();

            $temp = $this->isConflict($time_data, new Carbon($r_start), new Carbon($r_b_end));

            if ($temp) {
                return redirect()->back()->withErrors(["message" => "A time conflict was detected in your appointments. Please select a suitable time"]);
            } else {
                if ($timeConv->timeToSeconds($starting_t_x) >= $timeConv->timeToSeconds($user_p_info["info"]["work_from"]) && 
                    $timeConv->timeToSeconds($ending_t_x) <= $timeConv->timeToSeconds($user_p_info["info"]["work_to"])) {

                    DB::table($user_p_a_table)->insert([
                        'owner_id' => $user_p_id,
                        'app_id' => 'app_for',
                        'app_user_id' => $user_id,
                        'time_from' => $r_start,
                        'time_to' => $r_end,
                        'title' => $r_title,
                        'description' => $r_desc
                    ]);

                    DB::table($user_p_a_table)->insert([
                        'owner_id' => $user_p_id,
                        'app_id' => 'break_time',
                        'app_user_id' => 'xxxx',
                        'time_from' => $r_end,
                        'time_to' => $r_b_end,
                        'title' => 'break time',
                        'description' => 'break time'
                    ]);

                    DB::table($user_a_table)->insert([
                        'owner_id' => $user_id,
                        'app_id' => 'app_at',
                        'app_user_id' => $user_p_id,
                        'time_from' => $r_start,
                        'time_to' => $r_end,
                        'title' => $r_title,
                        'description' => $r_desc
                    ]);
                } else {
                    return redirect()->back()->withErrors(['message' => 'Sorry, the selected times are not within the user\'s work hours. Please select a suitable time period.']);
                }
            }
        }
    } else if ($r_date > $today) {
        if ($starting_t < $ending_t) {
            $time_data = DB::table($user_p_a_table)
                ->whereBetween("time_from", [$temp_r_date_1, $temp_r_date_2])
                ->get(["time_from", "time_to"])
                ->map(function($row) {
                    return ["start" => $row->time_from, "end" => $row->time_to];
                })->toArray();
                $date = Carbon::now();
                

            $temp = $this->isConflict($time_data, new Carbon($r_start), new Carbon($r_b_end));

            if ($temp) {
                return redirect()->back()->withErrors(['message' => 'A time conflict was detected in the other user\'s appointments. Please select a suitable time.']);
            } else {
                $time_data_2 = DB::table($user_a_table)
                    ->whereBetween("time_from", [$temp_r_date_1, $temp_r_date_2])
                    ->get(["time_from", "time_to"])
                    ->map(function($row) {
                        return ["start" => $row->time_from, "end" => $row->time_to];
                    })->toArray();

                $temp_2 = $this->isConflict($time_data_2, new Carbon($r_start), new Carbon($r_end));

                if ($temp_2) {
                    return redirect()->back()->withErrors(['message' => 'A time conflict was detected in your appointments. Please select a suitable time.']);
                } else {
                    if ($timeConv->timeToSeconds($starting_t_x) >= $timeConv->timeToSeconds($user_p_info["info"]->work_from) && 
                        $timeConv->timeToSeconds($ending_t_x) <= $timeConv->timeToSeconds($user_p_info["info"]->work_to)) {

                        DB::table($user_p_a_table)->insert([
                            'owner_id' => $user_p_id,
                            'app_id' => 'app_for',
                            'app_user_id' => $user_id,
                            'time_from' => $r_start,
                            'time_to' => $r_end,
                            'title' => $r_title,
                            'description' => $r_desc
                        ]);

                        DB::table($user_p_a_table)->insert([
                            'owner_id' => $user_p_id,
                            'app_id' => 'break_time',
                            'app_user_id' => 'xxxx',
                            'time_from' => $r_end,
                            'time_to' => $r_b_end,
                            'title' => 'break time',
                            'description' => 'break time'
                        ]);

                        DB::table($user_a_table)->insert([
                            'owner_id' => $user_id,
                            'app_id' => 'app_at',
                            'app_user_id' => $user_p_id,
                            'time_from' => $r_start,
                            'time_to' => $r_end,
                            'title' => $r_title,
                            'description' => $r_desc
                        ]);
                    } else {
                        return redirect()->back()->withErrors(['message' => 'Sorry, the selected times are not within the user\'s work hours. Please select a suitable time period.']);
                    }
                }
            }
        } else {
            return redirect()->back()->withErrors(['message' => 'The requested time is in the past. Please select a suitable time.']);
        }
    } else {
        return redirect()->back()->withErrors(['message' => 'The requested time is in the past. Please select a suitable time.']);
    }

    return response()->json([
        "message"=>"all good to go"
    ],200);
}


public function isConflict($timeData,$startingT,$endingT){
     // Ensure all times are valid Carbon objects
    if (!($startingT instanceof Carbon) || !($endingT instanceof Carbon)) {
        // Return false if arguments are not Carbon instances
        return false;
    }

    foreach ($timeData as $event) {
        if (!isset($event['start'], $event['end'])) {
            // Return false if time data elements are missing required keys
            return false;
        }

        // Convert event start and end times to Carbon instances
        $eventStart = Carbon::parse($event['start']);
        $eventEnd = Carbon::parse($event['end']);

        // Check for conflicts
        if (($startingT->lessThan($eventEnd) && $endingT->greaterThan($eventStart)) ||
            ($startingT->lessThanOrEqualTo($eventStart) && $endingT->greaterThanOrEqualTo($eventEnd))) {
            return true; // Conflict detected
        }
    }
    
    return false; // No conflicts found
}

private function checkWorkDay($r_date, $p_full_day) {
    $sessionCon = new SessionController;
    $data = [
        "key" => "user_p_info",
        "data" => ""
    ];

    $user_p_info = $sessionCon->session_selector("get", $data);
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

}