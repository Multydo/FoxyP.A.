<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\User;
use Auth;
use Illuminate\Support\Facades\Auth as FacadesAuth;
use PharIo\Manifest\Author;
use App\Http\Requests\LoginRequest;
use Laravel\Sanctum\PersonalAccessToken;
use Illuminate\Support\Carbon;
use App\Models\personal_access_token;
use App\Models\setting;
use App\Http\Controllers\API\getUserId;


class LoginControler extends Controller
{
    public function login(LoginRequest $user_data){
        $credentials = $user_data->only('email', 'password');
        if(Auth::attempt($credentials)){
            $user_email = $user_data['email'];
            $user_info = User::where('email' , $user_email)->first();
            $user_username = $user_info ->username;
            $user = Auth::user();

        
            $token = $user->createToken($user_username)->plainTextToken;

            $tokenStatus = personal_access_token::find( $token);

            $tokenStatus->last_used_at = Carbon::now();
            $tokenStatus->save();

            $timezone = $user_data->timezone;
            $status = $this->LogInTimeZone($token , $timezone);
            if($status){
                return response() -> json([
                    "massage"=>"user loged in",
                    "token" => $token
                ],200);
            }else{
                return response()->json([
                    "message"=>"error ????"
                ],500);
            }
            

        }else{
            return response()->json([
                "message"=>"email or password do not match"
            ],401);
        }
    }

    public function tokenLogin(Request $request){
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
                    $status = $this->LogInTimeZone($token , $timezone);
                    if ($status){
                        return response()->json([
                        "message"=>"user is good to go"
                    ],200);
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

    private function LogInTimeZone($token , $timezone){
        
        $userToken = $token;
        $getUserId = new getUserId;
        $userId = $getUserId->getId($userToken);
        $timezoneOffset = $timezone;
        $data = [
            "key" => "time_zone",
            "data" => "$timezoneOffset"
        ];
        $session_req = new Session_controler;
        $session_result = $session_req -> session_selector("put" , $data);

        if ($session_result){
            
            $status = Setting::where('owner_id', $userId)->update(['time_zone' => $timezoneOffset]);
            if($status){
                return true;
            }else{
                
                 return false;
            }

        }else{
            return false;
        
        }
    }
     
 }