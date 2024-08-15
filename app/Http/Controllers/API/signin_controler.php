<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\http\Controllers\API\signin_verification_code;
use App\Http\Controllers\API\Session_controler;
use App\Models\User;
use App\Http\Requests\SigninRequest;
use App\Http\Requests\SigninVCodeRequest;
use App\Http\Controllers\API\SetUserTables;

class signin_controler extends Controller
{

   
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store()
    {
        $data = [
            "key" => "user_data",
            "data" => ""
        ];
        
        $session_req = new Session_controler;
        $session_result = $session_req -> session_selector("get" , $data);
        //return response() -> json($session_result);
//dd($session_result);
        $new_user = User:: create ($session_result);
        $token = $new_user->createToken($session_result['username'])->plainTextToken;
        $setTables = new SetUserTables;
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
                "message" => "error in setting the user tables",
                "code" => 500,
                "status"=>false
            ];
            return $error;
        }
        
        
        
        
        
    }
    public function start_verification(SigninRequest $user_data_json){
        


        $user_data = $user_data_json->json()->all();
        $user_email = $user_data['email'];
        $user_name = $user_data['username'];

        $user_email_eixists = User::where('email',$user_email)->exists();
        if($user_email_eixists){
            return response()-> json([
                "error" => "user email alredy exists",
                
            ],409);
        }

        $user_name_eixists = User::where('email',$user_name)->exists();
        if($user_name_eixists){
            return response()-> json([
                "error" => "user name alredy exists",
                
            ],409);
        }



        $session_req = new Session_controler;
        $data = [
            "key" => "user_data",
            "data" => $user_data

        ];
        $session_result = $session_req -> session_selector("put", $data);
        if($session_result){

        
            $veri_code = new signin_verification_code;

            $veri_code_stat = $veri_code -> sendEmail($user_data);

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
     
        $session_req = new Session_controler;
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
            $session_req = new Session_controler;
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
    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}