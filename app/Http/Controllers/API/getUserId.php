<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;

class getUserId extends Controller
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
}