<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class testGetInfo extends Controller
{
    //
 
public function getUserInfo(Request $request)
{
    
    $token = $request->bearerToken();
    $user = User::whereHas('tokens', function ($query) use ($token) {
        $query->where('token',  $token); 
    })->first();

    if (!$user) {
        return response()->json(['error' => 'User not found'], 404);
    }

    return response()->json($user);
}
}