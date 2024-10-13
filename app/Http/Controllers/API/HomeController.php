<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Controllers\API\UserController;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Http\Controllers\API\tempsingin;

use Laravel\Sanctum\PersonalAccessToken;
use App\Models\personal_access_token;
use App\Models\setting;
use OpenApi\Annotations as OA;

/**
 * @OA\Tag(
 *     name="Home",
 *     description="Operations related to home functionalities"
 * )
 */
class HomeController extends Controller{

    /**
        * @OA\Post(
        *     path="/api/home",
        *     summary="Retrieve upcoming appointment dates",
        *     description="Validates the user's token and returns upcoming appointment dates if the user is verified.",
        *     operationId="master",
        *     tags={"Home"},
        *     @OA\RequestBody(
        *         required=true,
        *         @OA\JsonContent(
        *             @OA\Property(
        *                 property="timezone",
        *                 type="string",
        *                 description="User's timezone",
        *                 example="3"
        *             )
        *         )
        *     ),
        *     @OA\Response(
        *         response=200,
        *         description="Appointments found or not found",
        *         @OA\JsonContent(
        *             @OA\Property(property="message", type="string", example="appointments found for user"),
        *             @OA\Property(property="status", type="boolean", example=true),
        *             @OA\Property(
        *                 property="data",
        *                 type="array",
        *                 @OA\Items(type="string", example="2023-10-21")
        *             )
        *         )
        *     ),
        *     @OA\Response(
        *         response=401,
        *         description="Unauthorized or token expired",
        *         @OA\JsonContent(
        *             @OA\Property(property="message", type="string", example="token expired")
        *         )
        *     ),
        *     @OA\Response(
        *         response=500,
        *         description="Internal server error",
        *         @OA\JsonContent(
        *             @OA\Property(property="message", type="string", example="Internal server error")
        *         )
        *     ),
        *     security={
        *         {"bearerAuth": {}}
        *     }
        * )
    */
    public function master(Request $request){
         if (is_object($request) && method_exists($request, 'bearerToken')) {
    
            $token = $request->bearerToken();

            $tokenStatus = PersonalAccessToken::findToken($token);
            if($tokenStatus){
                $lastLogin = $tokenStatus->last_used_at;
                if($lastLogin && $lastLogin->lessThan(Carbon::now()->subWeeks(2))){
                    $tokenStatus -> delete();
                    return response()->json([
                        "message"=>"token expired"
                    ],401);
                }else if(!$lastLogin){
                     $tokenStatus -> delete();
                    return response()->json([
                        "massage"=>"internal server error (token last_used_at is null)"
                    ],500);
                }
                else{
                    $tokenStatus ->last_used_at = Carbon::now();
                    $tokenStatus->save();
                    $timezone = $request->timezone;
                    $loginCon = new UserController;
                    $status = $loginCon->LogInTimeZone($token , $timezone);
                    if ($status){
                       

                        $get_user_id = new UserController;
                        $userId = $get_user_id -> getId($request);
                        $user_info = User::where("id",$userId)->first();
                        if($user_info["verified"]){
                            $user_table = $userId."_app";
                            $todaydate =Carbon::now()->format('Y-m-d');
                            $dates =DB::table($user_table)
                                ->selectRaw('DISTINCT DATE(time_from) as date_part')
                                ->where('time_from','>=',$todaydate)
                                ->pluck('date_part')
                                ->toArray();
                            $name = User::where("id",$userId)->select("fname")->first();
                            $work_days = setting::where("owner_id",$userId)
                                ->select("monday","tuesday","wednesday","thursday","friday","saturday","sunday")
                                ->first();
                                $data = [
                                    "dates"=> $dates,
                                    "name"=>$name['fname'],
                                    "work_days"=>[
                                        "monday"=>$work_days['monday'],
                                        "tuesday"=>$work_days['tuesday'],
                                        "wednesday"=>$work_days['wednesday'],
                                        "thursday"=>$work_days['thursday'],
                                        "friday"=>$work_days['friday'],
                                        "saturday"=>$work_days['saturday'],
                                        "sunday"=>$work_days['sunday']
                                    ]  
                                ];
                            
                            if($dates){
                                return response()->json([
                                    "message" => "appointments found for user",
                                    "status" => true,
                                    "data" => $data
                                ],200);
                            }else{
                                return response()->json([
                                    "message" => "no appointments found for user",
                                    "status" => false,
                                    "data" => $data
                                ],200);
                            }

                        }else{
                            $user_data =[
                                "email"=>$user_info["email"],
                                "username"=>$user_info["username"]
                            ];
                            $sendemail = new tempsingin();
                            $sendemail->sendEmail( $user_data);
                            return response()->json([
                                "message"=>"user need to be verified to enter"
                            ],401);
                        }




                        







                    }else{
                        return response()->json([
                            "message"=>"error ????"
                        ],500);
                    }
                    
                }

                
            }else{
                return response()->json([
                    "message" => "token was not found"
                ],401);
            }





        }else {
            
           return response()->json([
                    "message" => "token was not found"
                ],401);
    
        }
        
    }

    /**
     * @OA\Post(
     *     path="/api/home/showDetails",
     *     summary="Retrieve appointment details for a specific date",
     *     description="Returns appointment details for the specified date if it's today or in the future.",
     *     operationId="showDetails",
     *     tags={"Home"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="r_date",
     *                 type="string",
     *                 format="date",
     *                 description="Requested date in YYYY-MM-DD format",
     *                 example="2023-10-21"
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
         *     description="Appointment details retrieved successfully",
         *     @OA\JsonContent(
         *         @OA\Property(
         *             property="time_data",
         *             type="array",
         *             @OA\Items(
         *                 type="object",
         *                 @OA\Property(property="start", type="string", example="09:00:00"),
         *                 @OA\Property(property="end", type="string", example="10:00:00"),
         *                 @OA\Property(property="app_id", type="string", example="1234"),
         *                 @OA\Property(property="title", type="string", example="Meeting with client"),
         *                 @OA\Property(property="desc", type="string", example="Discuss project requirements"),
         *                 @OA\Property(property="aid", type="integer", example=1),
         *                 @OA\Property(property="id", type="string", example="5678"),
         *                 @OA\Property(property="fname", type="string", example="John"),
         *                 @OA\Property(property="lname", type="string", example="Doe")
         *             )
         *         )
         *     )
         * ),
     *     @OA\Response(
     *         response=400,
     *         description="Bad Request - No appointments found",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Bad Request")
     *         )
     *     ),
     *     @OA\Response(
     *         response=418,
     *         description="Requested date is in the past",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="The requested date is in the past, are you a time traveler?")
     *         )
     *     ),
     *     security={
     *         {"bearerAuth": {}}
     *     }
     * )
     */
    public function showDetails(Request $request){

    
        $gmtCon = new TimeController;
        $get_user_id = new UserController;
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
                $timeZone = setting::where("owner_id",$user_id)->select("time_zone")->first();
                foreach ($appointments as $index => $row) {
                    $date_from = new Carbon($row->time_from);
                    $temp_d_f = $gmtCon->convertFromGMT($date_from->format('H:i:s'), $timeZone["time_zone"]);

                    $date_to = new Carbon($row->time_to);
                    $temp_d_t = $gmtCon->convertFromGMT($date_to->format('H:i:s'), $timeZone["time_zone"]);

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
                    }else{
                        $time_data[$index]['id'] = "-";
                        $time_data[$index]['fname'] = "-";
                        $time_data[$index]['lname'] = "-";
                    }
                }

            

                return response()->json([
                    "time_data" => $time_data
                ],200);
            }else{
                return response()->json([
                    "message"=>"Bad Request"
                ],400);
            }
        } else {
            return response()->json([
                "message"=>"the requested date is in the past , are you a time travler ?"
            ],418);
        }
    }
}