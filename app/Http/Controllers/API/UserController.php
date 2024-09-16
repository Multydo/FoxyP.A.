<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\http\Controllers\API\signin_verification_code;
use App\Http\Controllers\API\SessionController;
use App\Models\Otp;
use App\Http\Requests\SigninRequest;
use App\Http\Requests\SigninVCodeRequest;
use App\Http\Controllers\API\DynamicTableController;
use App\Mail\signin_verification_email_code;
use App\Mail\forgot_pass;
use Mail;

use Auth;
use App\Http\Requests\LoginRequest;
use Laravel\Sanctum\PersonalAccessToken;
use Illuminate\Support\Carbon;
use App\Models\personal_access_token;
use App\Models\setting;
use Throwable;

class UserController extends Controller
{
    public function getId($usertoken){
        if (is_object($usertoken) && method_exists($usertoken, 'bearerToken')) {
    
            $token = $usertoken->bearerToken();
        }else if (is_string($usertoken)) {
    
            $token = $usertoken;
        }else {
   
    
        }

        $tokenParts = explode('|', $token);
        if (count($tokenParts) !== 2) {
            return false;
        }

        $tokenId = $tokenParts[0]; // The ID part of the token
        $plainTextToken = $tokenParts[1]; // The plain text token part



        $user = User::whereHas('tokens', function ($query) use ($plainTextToken) {
            $query->where('token', hash('sha256', $plainTextToken)); 
        })->first();

        if (!$user) {
            return false;
        }
        $user_id = $user->id;

        return $user_id;
    }
    public function register(Request $request){
        $user_data = $request->all();
        
        $user_email = $user_data['email'];
        $user_name = $user_data['username'];

        $user_email_eixists = User::where('email',$user_email)->exists();
        
        if($user_email_eixists){
            $user_info = User::where('email',$user_email)->select("verified","username")->first();
            if($user_info["verified"]){
                return response()-> json([
                    "message" => "user email alredy exists",
                
                ],409);
            }else{
                
                $user_data=[
                    "email" => $request->email,
                    "username" => $user_info["username"]
                ];
                $this->sendEmail($user_data);
                return response()->json([
                    "message"=>"email already exists abut not verified , verification code sent"
                ],403);
            }
           
        }
        $user_name_eixists = User::where('username',$user_name)->exists();
        if($user_name_eixists){
            return response()-> json([
                "message" => "user name alredy exists",
                
            ],409);
        }


        $user = new User();
        $user->fname = $request->fname;
        $user->lname = $request->lname;
        $user->username = $request->username;
        $user->email = $request->email;
        $user->password = $request->password;
        
        $user->verified = false; 

         $user->save();

         $user_data=[
            "email" => $request->email,
            "username" => $request->username
         ];

         $token = $user->createToken($request['username'])->plainTextToken;

         $this->sendEmail($user_data);

         return response()->json([
            "message"=>"user registerd",
            "token"=>$token
         ],200);

        
    }

    public function sendEmail( $user_data){
        //dd("hi");
       try{
        $very_code = rand(1000,9999);
       $vcode = new Otp();
       $vcode->email = $user_data["email"];
       $vcode->code = $very_code;
       $vcode->save();


        
        
       

        $user_name = $user_data['username'];
        $user_email = $user_data['email'];

        
        $details = [
            'title' => "Verification Email: [company name] ",
            'body' => "Dear $user_name,

                Thank you for registering with [Your Company/Website Name]. To complete your sign-in process, please use the following verification code:

                Verification Code: [$very_code]

                Please enter this code on the sign-in page to verify your email address. If you did not request this code, please disregard this email.

                If you have any questions or need assistance, feel free to contact our support team.

                Best regards",
            'fname' => "$user_name",
            'verificationCode' => "$very_code"

        ];
      
        Mail::to("$user_email")->send(new signin_verification_email_code($details) );
       }catch(Throwable $e){
        return false;
       }
        
        return true ;
    }


    public function verify(Request $request){
        $user_side_verification_code = $request["code"];
        //dd($user_side_verification_code);
        $token = $request->bearerToken();
        $user_id = $this->getId($token);

        $user_email = User::where("id",$user_id)->select("email","verified")->first();
        if($user_email["verified"]){
            return response()->json([
                "message"=>"user alredy is verified"
            ],405);
        }else{
            $vcode = Otp::where("email",$user_email["email"])->select("code")->orderBy("id", "desc")->first();
        //dd($vcode["code"]);
        if($vcode["code"] == $user_side_verification_code){
            $setTables = new DynamicTableController;
            $status = $setTables ->UserTableLucher($request);

            $user = User::where("email",$user_email["email"])->first();
            $user->verified = true;
            $user->save();
            Otp::where("email", $user_email["email"])->delete();
            return response()->json([
                "message"=>"user good to go"
            ],201);
        }else{
            Otp::where("email", $user_email["email"])->delete();
            return response()->json([
                "message"=>"verification code does not match"
            ],400);
        }
        }


        
        
    }
    public function login(LoginRequest $user_data){
        $credentials = $user_data->only('email', 'password');
        if(Auth::attempt($credentials)){
            
            $user_email = $user_data['email'];
            $user_info = User::where('email' , $user_email)->first();
            $user_username = $user_info ->username;

            $old_token = personal_access_token::where("name",$user_username);
            if($old_token){
                $old_token -> delete();
            }

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
    public function LogInTimeZone($token , $timezone){
        
        $userToken = $token;
        $UserController = new UserController;
        $userId = $UserController->getId($userToken);
        
       
        try{
            $status = Setting::where('owner_id', $userId)->update(['time_zone' => $timezone]);
            return true;
        }catch(Throwable $e){
            return false;
        }
       
    }
    public function forgoPassword(Request $request){
        $token = $request->bearerToken();
        $userId = $this->getId($token);
        $userInfo = User::find($userId);
        
      //  dd($request->pass_1);
        $serverOtp = Otp::where("email",$userInfo->email)->select("code")->first();
        if($request->code == $serverOtp["code"]){
            $pass_1 = $request->pass_1;
            $pass_2= $request->pass_2;

            if($pass_1 == $pass_2){
                $userInfo ->password = $pass_1;
                $userInfo ->save();
                Otp::where("email",$userInfo["email"])->delete();
                return response()->json([
                "message"=>"new pass is set",
               
                ],201);
            }else{
                return response()->json([
                    "message"=>"passwords do not match"
                ],403);
            }
        }else{
            Otp::where("email",$userInfo["email"])->delete();
            return response()->json([
                "message"=>"verification code does not match"
            ],401);
        }
    }

    public function forgotPasswordCode(Request $request){
        
        $token = $request->bearerToken();
        $userId = $this->getId($token);

        $userInfo = User::where("id",$userId)->select("email","username")->first();
        $user_name = $userInfo['username'];
        $user_email = $userInfo['email'];
       // dd($userInfo);
        try{
            $very_code = rand(1000,9999);
            $vcode = new Otp();
            $vcode->email = $userInfo["email"];
            $vcode->code = $very_code;
            $vcode->save();


        
        
        
            $details = [
            
                'fname' => "$user_name",
                'resetCode' => "$very_code"

            ];
    
        Mail::to("$user_email")->send(new forgot_pass($details) );
       }catch(Throwable $e){
        return false;
       }
        
        return true ;
    }
}