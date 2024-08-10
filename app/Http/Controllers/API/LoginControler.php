<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\User;
use Auth;
use Illuminate\Support\Facades\Auth as FacadesAuth;
use PharIo\Manifest\Author;
use App\Http\Requests\LoginRequest;

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

        return response() -> json([
            'status' => true,
            
            'token' => $token,
            'message' => 'user loged in '
        ]);

     }else{
        return response()->json([
            'status' => false,
            'message'=>'user email or password is wrong'
        ]);
     }
 }



}