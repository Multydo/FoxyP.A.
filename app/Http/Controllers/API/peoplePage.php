<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Controllers\API\getUserId;
use Illuminate\Support\Facades\DB;

class peoplePage extends Controller
{
    public function getFollowing(Request $request){
        $getId = new getUserId;
        $userId= $getId-> getId($request);

        if($userId){

            $userTable = $userId . "_people";


            $follow_data = DB::table('users as u')
    ->join($userTable . ' as f', 'u.id', '=', 'f.user_f_id')
    ->where('f_status', 'following')
    ->select('u.fname', 'u.lname', 'u.id')
    ->get()
    ->toArray();

if (empty($follow_data)) {
    $follow_data[] = '0';
}

dd($follow_data);
        }else{
            return response()->json([
                "message"=>"user not found or token is broken"

            ],401);
        }
    }
}