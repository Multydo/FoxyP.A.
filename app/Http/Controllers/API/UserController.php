<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\http\Controllers\API\signin_verification_code;
use App\Http\Controllers\API\SessionController;

use App\Http\Requests\SigninRequest;
use App\Http\Requests\SigninVCodeRequest;
use App\Http\Controllers\API\DynamicTableController;
use App\Mail\signin_verification_email_code;
use Mail;

use Auth;
use App\Http\Requests\LoginRequest;
use Laravel\Sanctum\PersonalAccessToken;
use Illuminate\Support\Carbon;
use App\Models\personal_access_token;
use App\Models\setting;



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
     public function store()
    {
        $data = [
            "key" => "user_data",
            "data" => ""
        ];
        
        $session_req = new SessionController;
        $session_result = $session_req -> session_selector("get" , $data);
        //return response() -> json($session_result);
//dd($session_result);
        $new_user = User:: create ($session_result);
        $token = $new_user->createToken($session_result['username'])->plainTextToken;
        $setTables = new DynamicTableController;
        $status = $setTables ->UserTableLucher($token);

        if($status){
            $result = [
                "message" => "tables are done",
                "token" => $token,
                "code" => 201,
                "status"=>true
            ];


          return $result ;
          
        }else{
            $error = [
                "message" => "internal server error (error in setting the user tables)",
                "code" => 500,
                "status"=>false
            ];
            return $error;
        }
        
        
        
        
        
    }
    public function start_verification(SigninRequest $request){
        


        $user_data = $request->all();
        
        $user_email = $user_data['email'];
        $user_name = $user_data['username'];

        $user_email_eixists = User::where('email',$user_email)->exists();
        if($user_email_eixists){
            return response()-> json([
                "message" => "user email alredy exists",
                
            ],409);
        }

        $user_name_eixists = User::where('username',$user_name)->exists();
        if($user_name_eixists){
            return response()-> json([
                "message" => "user name alredy exists",
                
            ],409);
        }



        $session_req = new SessionController;
        $data = [
            "key" => "user_data",
            "data" => $user_data

        ];
        $session_result = $session_req -> session_selector("put", $data);
        if($session_result){

        
            

            $veri_code_stat = $this -> sendEmail($user_data);

            if($veri_code_stat){
                return response()->json([
                "messange" => "email sent sucsessfuly",

                ],200);
            }else{
                return response()->json([
                "message"=>"internal server error (mail not sent)"

                ],500);
            }

        
        }else{
            return response()->json([
               "massage"=>"internal server error (problem in session)" 
            ],500);
        }
        

    }
     public function check_code(SigninVCodeRequest $user_side_verification_code_json){
        $user_side_verification_code = $user_side_verification_code_json ->json()->all();
     
        $session_req = new SessionController;
        $data = [
            "key" => "very_code",
            "data" => ""

        ];
        $session_result = $session_req -> session_selector("get" ,$data );
        if($session_result == $user_side_verification_code["code"]){

         // if they mach sign in
            $status = $this -> store();
            if($status['status']){
                return response()->json([
                    "message"=>$status['message'],
                    "token" => $status['token'],

                ],$status['code']);
            }else{
                return response()->json([
                    "message"=>$status['message'],
            

                ],$status['code']);
            }

        
        }else{

            // flush data down :)
            $session_req = new SessionController;
            $data = [
                "key" => "",
                "data" => ""

            ];
            $session_result = $session_req -> session_selector("flush" , $data);
            if($session_result){
                 return response()->json([
                    "message"=>"verification code does not match"
                ],401);
                
            }else{
               return response()->json([
                "message"=>"internal server error (problem in session)"
               ],500);
               
            }
        }


    }
    public function sendEmail( $user_data){
        //dd("hi");
       
        $very_code = rand(1000,9999);
        $data = [
            "key" => "very_code",
            "data" => "$very_code"

        ];
        $session_req = new SessionController;
        $session_result = $session_req -> session_selector("put" , $data);
        
       

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
        return true ;
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
        $timezoneOffset = $timezone;
        $data = [
            "key" => "time_zone",
            "data" => "$timezoneOffset"
        ];
        $session_req = new SessionController;
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