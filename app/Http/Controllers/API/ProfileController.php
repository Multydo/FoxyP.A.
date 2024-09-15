<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\http\Controllers\API\UserController;
use App\Models\Otp;
use Illuminate\Support\Facades\Hash;


class ProfileController extends Controller
{
    public function getProfile(Request $request){
        $userCon = new UserController;
        $token = $request->bearerToken();
        $userId = $userCon->getId($token);

        $userInfo = User::where("id",$userId)
            ->select("fname","lname","username","email")->first();
        return response()->json([
            "message"=>"user data found",
            "data"=>$userInfo
        ],200);
    }
    public function changeName(Request $request){
        $userCon = new UserController;
        $token = $request->bearerToken();
        $userId = $userCon->getId($token);

        $userInfo = User::where("id",$userId)->first();
        $userInfo->fname = $request->fname;
        $userInfo->lname = $request->lname;
        $userInfo->save();

        return response()->json([
            "message"=>"user name saves"

        ],200);
    }
    public function changeEmail(Request $request){
        $userCon = new UserController;
        $token = $request->bearerToken();
        $userId = $userCon->getId($token);
        $newEmail = $request->email;
        

        $userInfo = User::where("id",$userId)->first();
        $state = User::where("email",$newEmail)->first();
        if($state){
            return response()->json([
                "message"=>"email alresy in use"
            ],401);
        }else{
            $userInfo->temp_email = $request->email;
            
            $userInfo->save();

            $username = User::where("id",$userId)->select("username")->first();
            $user_data = [
                "email"=>$newEmail,
                "username"=>$username["username"]
            ];

            $stateOtp = $userCon->sendEmail($user_data);
            if($stateOtp){
                return response()->json([
                    "message"=>"user email saved and need verification"
                    ],200);
            }else{
                return response()->json([
                    "message"=>"internal server error (problem in sending otp email)"
                ],500);
            }

            
             
        }
        
    }

    public function verifyNewEmail(Request $request){
        $user_side_verification_code = $request["code"];
        //dd($user_side_verification_code);
        $token = $request->bearerToken();
        $userCon = new UserController;
        $user_id = $userCon->getId($token);

        $userInfo = User::find($user_id);
        
            $vcode = Otp::where("email",$userInfo->temp_email)->select("code")->orderBy("id", "desc")->first();
        //dd($vcode["code"]);
        if($vcode["code"] == $user_side_verification_code){
            
            $userInfo->email = $userInfo -> temp_email;
            $userInfo->save();
            Otp::where("email", $userInfo["temp_email"])->delete();
            return response()->json([
                "message"=>"user new email is set to sart using it"
            ],201);

            
        }else{
            Otp::where("email", $userInfo["temp_email"])->delete();
            return response()->json([
                "message"=>"verification code does not match"
            ],400);
        }   
    }

    public function changePassword(Request $request){
        $userCon = new UserController;
        $token = $request->bearerToken();
        $userId = $userCon->getId($token);

        $userInfo = User::find($userId);
        if(Hash::check($request->old_password, $userInfo->password)){
            if($request->new_pass_1 == $request->new_pass_2){
                $userInfo->password  = $request->new_pass_1;
                $userInfo->save();
                return response()->json([
                    "message"=>"new pass is set"
                ],201);
            }else{
                return response()->json([
                    "message"=>"the new pass does not match"
                ],403);
            }
        }else{
            return response()->json([
                "message"=>"old pass does not match"
            ],401);
        }
    }
}