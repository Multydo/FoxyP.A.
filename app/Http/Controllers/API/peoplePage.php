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
    public function followPerson(Request $request){
        $token = $request -> bearerToken();
        $get_user_id = new getUserId;
        $userId = $get_user_id -> getId($token);
        $requestData = $request->json()->all();
        $userFId = $requestData["other_user"];

        $userTable = $userId."_people";
        $userFTable = $userFId."_people";

        $f_status2 = "following";
        DB::table($userTable)->insert([
            'owner_id' => $userId,
            'f_status' => $f_status2,
            'user_f_id' => $userFId,
        ]);

        // Update following_num in users_follower
        $result21 = DB::table('followers_users')
            ->where('userId', $userId)
            ->increment('following_num', 1);

        // Second Insert Operation
        $f_status3 = "follower";
        DB::table($userFTable)->insert([
            'owner_id' => $userFId,
            'f_status' => $f_status3,
            'user_f_id' => $userId,
        ]);

        // Update followers_num in users_follower
        $result31 = DB::table('followers_users')
            ->where('userId', $userFId)
            ->increment('followers_num', 1);
        
    }

    public function unfollowPeople(Request $request){
        $token = $request -> bearerToken();
        $get_user_id = new getUserId;
        $userId = $get_user_id -> getId($token);
        $requestData = $request->json()->all();
        $userFId = $requestData["other_user"];

        $userTable = $userId."_people";
        $userFTable = $userFId."_people";

        DB::table($userTable)->where('user_f_id', $userFId)->delete();
        DB::table($userFTable)->where('user_f_id', $userId)->delete();

        // Select owner_id counts
        $num_row_p = DB::table($userTable)->count();
        $num_row_f_p = DB::table($userFTable)->count();

        // Update following_num in users_follower for the user_id
        DB::table('followers_users')
        ->where('userId', $userId)
       ->update(['following_num' => $num_row_p]);

        // Update following_num in users_follower for the user_f_id
        DB::table('followers_users')
        ->where('userId', $userFId)
        ->update(['following_num' => $num_row_f_p]);
    }

    public function searchPeople(Request $request){
         $token = $request -> bearerToken();
        $get_user_id = new getUserId;
        $userId = $get_user_id -> getId($token);
        $requestData = $request->json()->all();
        
        $userTable = $userId."_people";
        $gues = $requestData['search'];

        $search_data = DB::table('users as u')
    //->join('followers_users as f', 'u.id', '=', 'f.userId')
    ->leftJoin($userTable . ' as p', 'p.user_f_id', '=', 'u.id')
    ->select( 'u.fname', 'u.lname', 'p.f_status')
    ->where(function($query) use ($gues) {
        $query->where('u.fname', 'like', $gues . '%')
              ->orWhere('u.lname', 'like', $gues . '%')
              ->orWhere('u.fname', '=', $gues)
              ->orWhere('u.lname', '=', $gues)
              ->orWhere('u.username', '=', $gues);
    })
    ->where('u.id', '<>', $userId)
    ->get()
    ->toArray();

// Check if there are results
if (empty($search_data)) {
    $search_data[] = '0';
}

return response()->json([$search_data]);
    }
}