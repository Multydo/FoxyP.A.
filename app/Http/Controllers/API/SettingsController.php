<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\http\Controllers\API\UserController;
use App\Models\setting;
use App\Http\Requests\settingsRequest;
use Illuminate\Support\Carbon;

/**
 * @OA\Tag(
 *     name="Settings",
 *     description="Operations related to user settings"
 * )
 */
class SettingsController extends Controller
{

    /**
     * @OA\Post(
     *     path="/api/settings/getSettings",
     *     summary="Get user settings",
     *     description="Retrieves the settings for the authenticated user.",
     *     operationId="getSettings",
     *     tags={"Settings"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Response(
     *         response=200,
     *         description="User settings retrieved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="user settings were found"),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="owner_id", type="integer", example=1),
     *                 @OA\Property(property="work_from", type="string", example="08:00:00"),
     *                 @OA\Property(property="work_to", type="string", example="17:00:00"),
     *                 @OA\Property(property="time_zone", type="string", example="UTC"),
     *                 @OA\Property(property="break_time", type="string", example="00:30:00"),
     *                 @OA\Property(property="max_app", type="integer", example=10),
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
     *         response=204,
     *         description="No content found",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="no content found")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized or broken token",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="user was not found or a broken token")
     *         )
     *     )
     * )
     */
    public function getsettings(Request $request){
        $get_user_id = new UserController;
        $userId = $get_user_id -> getId($request);
        if($userId){
            $data = setting::where('owner_id',$userId)->first();
            if($data){
                return response()->json([
                    "message" => "user settings were found",

                    "data" => $data
                ],200);
            }else{
                return response()->json([
                    "message"=>"no content found"

                ],204);
            }
        }else{
            return response()->json([
                "message"=>"user was not found or a broken token"
            ],401);
        }
    }

    /**
     * @OA\Post(
     *     path="/api/settings/saveSettings",
     *     summary="Save user settings",
     *     description="Saves the settings for the authenticated user.",
     *     operationId="saveSettings",
     *     tags={"Settings"},
     *     security={{"bearerAuth": {}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="work_from", type="string", example="08:00:00"),
     *             @OA\Property(property="work_to", type="string", example="17:00:00"),
     *             @OA\Property(property="time_zone", type="string", example="UTC"),
     *             @OA\Property(property="break_time", type="string", example="00:30:00"),
     *             @OA\Property(property="max_app", type="integer", example=10),
     *             @OA\Property(property="monday", type="string", example="accepted"),
     *             @OA\Property(property="tuesday", type="string", example="accepted"),
     *             @OA\Property(property="wednesday", type="string", example="accepted"),
     *             @OA\Property(property="thursday", type="string", example="accepted"),
     *             @OA\Property(property="friday", type="string", example="accepted"),
     *             @OA\Property(property="saturday", type="string", example="closed"),
     *             @OA\Property(property="sunday", type="string", example="closed")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Settings saved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="settings are saved")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal server error",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="internal server error (problem is unclear)")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized or broken token",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="user was not found or a broken token")
     *         )
     *     )
     * )
     */

    public function saveSettings(Request $request){
       
        $token = $request->bearerToken();
        $get_user_id = new UserController;

        $userId = $get_user_id -> getId($token);
        if($userId){
            $data = $request->json()->all();
            
            $user_settings = setting::where("owner_id",$userId)->first();

            if($user_settings){
                $user_settings -> work_from = $data["work_from"];
                $user_settings -> work_to = $data["work_to"];
                $user_settings -> break_time  = $data["break_time"];
                $user_settings -> time_zone = $data["time_zone"];
                $user_settings -> logic = $data["logic"];
                $user_settings -> max_app = $data["max_app"];
                $user_settings -> monday = $data["monday"];
                $user_settings -> tuesday = $data["tuesday"];
                $user_settings -> wednesday = $data["wednesday"];
                $user_settings -> thursday = $data["thursday"];
                $user_settings -> friday = $data["friday"];
                $user_settings -> saturday = $data["saturday"];
                $user_settings -> sunday = $data["sunday"];
                $user_settings -> max_duration_swicth = $data["max_duration_swicth"];
                $user_settings -> max_duration_time = $data["max_duration_time"];
                $user_settings ->min_time_switch = $data["min_time_switch"];
                $user_settings ->min_time = $data["min_time"];
                $user_settings -> app_fixed_duration_switch = $data["app_fixed_duration_switch"];
                $user_settings -> app_fixed_duration = $data["app_fixed_duration"];
                $user_settings -> allow_dm = $data["allow_dm"];
                $user_settings -> updated_at = Carbon::now();
                $user_settings ->save();
                
                return response()->json([
                    "message"=>"setting are saved"

                ],200);
            }else{
                return response()->json([
                    "message"=>"internal server error (problem is unclear)"
                ],500);
            }

        }else{
            return response()->json([
                "message"=>"user was not found or a broken token"
            ],401);
        }
    }
    
}