<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\http\Controllers\API\signin_verification_code;
use App\Http\Controllers\API\Session_controler;
use App\Models\User;
use App\Http\Requests\SigninRequest;
use App\Http\Requests\SigninVCodeRequest;

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

        
        
        
        
        
        
    }
    public function start_verification(SigninRequest $user_data_json){

        $user_data = $user_data_json->json()->all();
        $session_req = new Session_controler;
         $data = [
            "key" => "user_data",
            "data" => $user_data

        ];
        $session_result = $session_req -> session_selector("put", $data);
        if($session_result){

        
        $veri_code = new signin_verification_code;

        $veri_code_stat = $veri_code -> sendEmail($user_data);

        return "all set to check ,  $veri_code_stat ";
        }else{
            return "problem in session";
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
        $this -> store();
    }else{

        // flush data down :)
        $session_req = new Session_controler;
         $data = [
            "key" => "",
            "data" => ""

        ];
        $session_result = $session_req -> session_selector("flush" , $data);
        if($session_result){
        return "code does not mach !!";
    }else{
        return "WTF is going on";
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