<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\HTTP\Controllers\API\TimeController;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\API\UserController;
use Throwable;
use App\Models\setting;
use Carbon\Carbon;

/**
 * @OA\Tag(
 *     name="Request",
 *     description="Operations related to appointment requests"
 * )
 */
class RequestController extends Controller
{
    /**
     * @OA\Post(
     *     path="/api/requests/setrequest",
     *     summary="Set up a request",
     *     description="Sets up a request and returns necessary information about the user and their availability.",
     *     operationId="setRequest",
     *     tags={"Request"},
     *     security={{"bearerAuth": {}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"other_user"},
     *             @OA\Property(property="other_user", type="integer", description="ID of the other user", example=2)
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Request information retrieved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="userId", type="integer", example=2),
     *             @OA\Property(
     *                 property="info",
     *                 type="object",
     *                 description="User's work hours and time zone",
     *                 @OA\Property(property="work_from", type="string", example="08:00:00"),
     *                 @OA\Property(property="work_to", type="string", example="17:00:00"),
     *                 @OA\Property(property="time_zone", type="string", example="UTC")
     *             ),
     *             @OA\Property(
     *                 property="full_days",
     *                 type="array",
     *                 @OA\Items(type="string", format="date", example="2023-10-21")
     *             ),
     *             @OA\Property(
     *                 property="info_day",
     *                 type="object",
     *                 description="User's work days",
     *                 @OA\Property(property="monday", type="string", example="accepted"),
     *                 @OA\Property(property="tuesday", type="string", example="accepted"),
     *                 @OA\Property(property="wednesday", type="string", example="accepted"),
     *                 @OA\Property(property="thursday", type="string", example="accepted"),
     *                 @OA\Property(property="friday", type="string", example="accepted"),
     *                 @OA\Property(property="saturday", type="string", example="closed"),
     *                 @OA\Property(property="sunday", type="string", example="closed")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Unauthorized")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal server error",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="An error occurred")
     *         )
     *     )
     * )
     */
    public function setRequest(Request $request){
        $user_p_info = [];
        $token = $request -> bearerToken();
        $UserController = new UserController;
        
        $gmtcon =new TimeController;
        $user_id = $UserController -> getId($token);
        $user_f_id = $request["other_user"];
        /*if($request->has("other_user")){
            
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
        }*/

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
        $timezone = setting::where("owner_id",$user_id)->select("time_zone")->first();

        $info->work_from = $gmtcon->convertFromGMT($info->work_from , $timezone);
        $info->work_to = $gmtcon->convertFromGMT($info->work_to , $timezone);

         $user_p_info['info'] = $info;

        $info_day = setting::where("owner_id", $user_f_id)
        ->select("monday","tuesday","wednesday","thursday","friday","saturday","sunday")
        ->first()
        ->toArray();

        $user_p_info["info_day"]= $info_day;

       

        return response()->json([
            "userId"=>$user_f_id,
            "info"=>$info,
            "full_days"=>$full_days,
            "info_day"=>$info_day
        ]);
    }

    /**
     * @OA\Post(
     *     path="/api/requests/checkDate",
     *     summary="Check date availability",
     *     description="Checks if a specific date is available for an appointment with the specified user.",
     *     operationId="checkDateAvailability",
     *     tags={"Request"},
     *     security={{"bearerAuth": {}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"userId", "r_date"},
     *             @OA\Property(property="userId", type="integer", description="ID of the user to check availability with", example=2),
     *             @OA\Property(property="r_date", type="string", format="date", description="Requested date in YYYY-MM-DD format", example="2023-10-25")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Date is available",
     *         @OA\JsonContent(
     *             @OA\Property(property="userId", type="integer", example=2),
     *             @OA\Property(property="r_date", type="string", format="date", example="2023-10-25"),
     *             @OA\Property(
     *                 property="time_data",
     *                 type="array",
     *                 @OA\Items(
     *                     @OA\Property(property="start", type="string", example="09:00:00"),
     *                     @OA\Property(property="end", type="string", example="10:00:00")
     *                 )
     *             ),
     *             @OA\Property(
     *                 property="work_data",
     *                 type="array",
     *                 @OA\Items(
     *                     @OA\Property(property="start", type="string", example="08:00:00"),
     *                     @OA\Property(property="end", type="string", example="17:00:00")
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Bad Request - Date is in the past",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="The requested date is in the past. Please select a suitable date.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=409,
     *         description="Conflict - Date is full or closed",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="The requested date is full/closed. Please select a different date.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Unauthorized")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal server error",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="An error occurred")
     *         )
     *     )
     * )
     */


    public function checkDateAvailability(Request $request) {
        $token = $request->bearerToken();
        $userCon = new UserController;
        $main_user_id = $userCon->getId($token);

        $gmtCon = new TimeController;

        $user_p_id = $request->userId;
        $r_date = $request->r_date;
        $max_app_f = setting::where("owner_id", $user_p_id)->value("max_app");
        $user_f_a_table = $user_p_id . "_app";
        $p_full_day = DB::table($user_f_a_table)
            ->select(DB::raw('DATE(time_from) AS date_part, COUNT(*) AS count'))
            ->where('app_user_id', '<>', 'xxxx')
            ->groupBy('date_part')
            ->having('count', '>=', $max_app_f)
            ->pluck('date_part')
            ->toArray();

        $today = Carbon::now()->format('Y-m-d');

        if ($r_date >= $today) {
            if (!in_array($r_date, $p_full_day)) {
                if ($this->checkWorkDay($r_date, $p_full_day,$user_p_id)) {
                    $user_p_a_table = $user_p_id . "_app";
                    $temp_r_date_1 = $r_date . " 00:00:00";
                    $temp_r_date_2 = $r_date . " 23:59:59";

                    $appointments = DB::table($user_p_a_table)
                        ->where('time_from', '>=', $temp_r_date_1)
                        ->where('time_to', '<=', $temp_r_date_2)
                        ->get(['time_from', 'time_to']);

                    $time_data = [];

                    $timezone = setting::where("owner_id",$main_user_id)->value("time_zone");

                    foreach ($appointments as $index => $row) {
                        $time_data[$index]['start'] = $gmtCon->convertFromGMT(Carbon::parse($row->time_from)->format('H:i:s'), $timezone);
                        $time_data[$index]['end'] = $gmtCon->convertFromGMT(Carbon::parse($row->time_to)->format('H:i:s'), $timezone);
                    }
                    $info = setting::where('owner_id', $user_p_id)
                        ->select('work_from', 'work_to', 'time_zone')
                        ->first();
                        $timeCon = new TimeController;
                        $work_from = $timeCon ->convertFromGMT($info->work_from, $timezone);
                        $work_to = $timeCon ->convertFromGMT($info->work_to, $timezone);



                    $work_data = [
                        ['start' => $work_from, 'end' => $work_to]
                    ];

                    return response()->json([
                        "userId"=>$user_p_id,
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

        } else {
            return response()->json([
                'message' => 'The requested date (' . $r_date . ') is in the past. Please select a suitable date.'
            ], 400);
        }
    }
    /**
     * @OA\Post(
     *     path="/api/requests/sendRequest",
     *     summary="Send an appointment request",
     *     description="Sends a request to schedule an appointment with the specified user.",
     *     operationId="sendRequest",
     *     tags={"Request"},
     *     security={{"bearerAuth": {}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"userId", "r_date", "starting_t", "ending_t", "r_title", "r_desc"},
     *             @OA\Property(property="userId", type="integer", description="ID of the user to send the request to", example=2),
     *             @OA\Property(property="r_date", type="string", format="date", description="Requested date in YYYY-MM-DD format", example="2023-10-25"),
     *             @OA\Property(property="starting_t", type="string", description="Starting time in HH:MM:SS format", example="10:00:00"),
     *             @OA\Property(property="ending_t", type="string", description="Ending time in HH:MM:SS format", example="11:00:00"),
     *             @OA\Property(property="r_title", type="string", description="Title of the appointment", example="Meeting"),
     *             @OA\Property(property="r_desc", type="string", description="Description of the appointment", example="Discuss project requirements")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Appointment request sent successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="all good to go")
     *         )
     *     ),
     *     @OA\Response(
     *         response=409,
     *         description="Conflict - Time input wrong or appointment conflicts",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="time inputted wrong or appointment time conflicts with other appointments")
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Bad Request - Time is in the past",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="The requested time is in the past. Please select a suitable time.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Unauthorized")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal server error",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="An error occurred")
     *         )
     *     )
     * )
     */






    

    public function sendRequest(Request $request) {
        // Get token and user ID once


        $token = $request->bearerToken();
        $userCon = new UserController;
        $main_user_id = $userCon->getId($token);

        // Setup controllers and variables

        $gmtCon = new TimeController;

        $user_p_id = $request->userId;
        $info = setting::where('owner_id', $user_p_id)
                        ->select('work_from', 'work_to', 'time_zone')
                        ->first();
        $r_date = $request->r_date;
        $starting_t_x = $request->starting_t;
        $ending_t_x = $request->ending_t;
        $r_title = $request->r_title;
        $r_desc = $request->r_desc;

        // Timezone conversion
        $timezone = setting::where("owner_id", $main_user_id)->value("time_zone");
        $starting_t = $gmtCon->convertToGMT($starting_t_x, $timezone);
        $ending_t = $gmtCon->convertToGMT($ending_t_x, $timezone);

        // Variables for date and time calculations
        $user_p_a_table = $user_p_id . "_app";
        $user_a_table = $main_user_id . "_app";
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
        //dd("hi");
        // Check time conflicts
        $time_data = $this->getTimeData($user_p_a_table, $temp_r_date_1, $temp_r_date_2);
        $temp = $this->isConflict($time_data, new Carbon($r_start), new Carbon($r_b_end));

        if ($r_date == $today && $starting_t_x >= $current_time) {
            if ($starting_t < $ending_t) {
                $state =  $this->handleInsertions($temp,  $info, $user_p_a_table, $user_a_table, $user_p_id, $main_user_id, $r_start, $r_end, $r_b_end, $r_title, $r_desc, $starting_t_x, $ending_t_x);
                //dd($state);
                if ($state){
                    return response()->json(["message" => "all good to go"], 200);
                
                }else{
                    return response()->json([
                        "message"=>"time inputed wrong or appointment time conflicts with other appointments"
                    ],409);
                }
            }
        } else if ($r_date > $today) {
            if ($starting_t < $ending_t) {
                $time_data_2 = $this->getTimeData($user_a_table, $temp_r_date_1, $temp_r_date_2);
                $temp_2 = $this->isConflict($time_data_2, new Carbon($r_start), new Carbon($r_end));
                 $state = $this->handleInsertions($temp_2,  $info, $user_p_a_table, $user_a_table, $user_p_id, $main_user_id, $r_start, $r_end, $r_b_end, $r_title, $r_desc, $starting_t, $ending_t);
                //dd($state);
                 if ($state){
                    return response()->json(["message" => "all good to go"], 200);
                }else{
                    return response()->json([
                        "message"=>"time inputed wrong or appointment time conflicts with other appointments"
                    ],409);
                }
            }
        } else {
            return response()->json(['message' => 'The requested time is in the past. Please select a suitable time.']);
        }


    }

    // Helper function to get time data
    private function getTimeData($table, $start, $end) {
        return DB::table($table)
            ->whereBetween("time_from", [$start, $end])
            ->get(["time_from", "time_to"])
            ->map(function($row) {
                return ["start" => $row->time_from, "end" => $row->time_to];
            })->toArray();
    }

    // Handle the insertions to the DB
    private function handleInsertions($conflict, $info, $user_p_a_table, $user_a_table, $user_p_id, $user_id, $r_start, $r_end, $r_b_end, $r_title, $r_desc, $starting_t_x, $ending_t_x) {
         $timeConv = new TimeController;
         $timezone = setting::where("owner_id",$user_id)->value("time_zone");
     $timeCon = new TimeController;
                        $work_from = $timeCon ->convertFromGMT($info->work_from, $timezone);
                        $work_to = $timeCon ->convertFromGMT($info->work_to, $timezone);
       //dd($conflict, $info, $user_p_a_table, $user_a_table, $user_p_id, $user_id, $r_start, $r_end, $r_b_end, $r_title, $r_desc, $starting_t_x, $ending_t_x);
        if ($conflict) {
            return false;
        } else {
            if ($timeConv->timeToSeconds($starting_t_x) >= $timeConv->timeToSeconds($work_from) &&
                $timeConv->timeToSeconds($ending_t_x) <= $timeConv->timeToSeconds($work_to)) {

                // Insert appointment and break time
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
                return true;
            } else {
                return false;
            }
        }
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

    private function checkWorkDay($r_date, $p_full_day,$user_id) {




        $info_day = setting::where("owner_id", $user_id)
            ->select("monday","tuesday","wednesday","thursday","friday","saturday","sunday")
            ->first()
            ->toArray();
        //dd($info_day);

        $day_num = Carbon::parse($r_date)->dayOfWeek;
        $days_list = [
            $info_day["sunday"],
            $info_day["monday"],
            $info_day["tuesday"],
            $info_day["wednesday"],
            $info_day["thursday"],
            $info_day["friday"],
            $info_day["saturday"],
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