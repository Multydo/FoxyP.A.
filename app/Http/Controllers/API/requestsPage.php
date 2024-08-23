<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Controllers\API\Session_controler;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\API\getUserId;
use Throwable;
use App\Models\setting;

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

        $max_app_f = setting::where("owner_id",$user_f_id)->select("max_app");
        $max_app = setting::where("owner_id" ,$user_id)->select("max_app");
        
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
        ->join("settings" . ' as s', 'u.id', '=', 's.owner_id')
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

}