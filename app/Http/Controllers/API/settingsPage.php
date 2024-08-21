<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\http\Controllers\API\getUserId;
use App\Models\setting;
use App\Http\Requests\settingsRequest;


class settingsPage extends Controller
{
    public function getsettings(Request $request){
        $get_user_id = new getUserId;
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

    public function saveSettings(Request $request){
        $token = $request->bearerToken();
        $get_user_id = new getUserId;
        $userId = $get_user_id -> getId($token);
        if($userId){
            $data = $request->json()->all();
            $status = setting::where("owner_id",$userId)->update($data);
            if($status){
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
        
/*
        public function saveSettings(Request $request)
{
    // Ensure the request is authenticated via Sanctum
    $userId = auth()->id();

    if (!$userId) {
        return response()->json([
            "message" => "User not authenticated"
        ], 401);
    }

    // Retrieve the validated data from the request
    $data = $request->validated();

    // Update the settings for the authenticated user
    $status = setting::where("owner_id", $userId)->update($data);

    if ($status) {
        return response()->json([
            "message" => "Settings have been saved"
        ], 200);
    } else {
        return response()->json([
            "message" => "Internal server error (problem is unclear)"
        ], 500);
    }
}*/
}