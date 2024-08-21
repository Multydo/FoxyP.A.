<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\setting;
use Throwable;
use App\Models\followers_user;
use App\Http\Controllers\API\getUserId;

class SetUserTables extends Controller
{
    public function UserTableLucher( $usertoken){
        $get_user_id = new getUserId;
        $userId_json = $get_user_id -> getId($usertoken);
        $userId = $userId_json;
        
        $result1 = $this -> UserAppointments($userId);
        $result2 = $this -> UserPeople($userId);
        $result3 = $this -> UserFollower($userId);

        if ($result1 && $result2 && $result3){
            return true;
        }else{
            return false;
        }

    }
   /* 
    private function getId($usertoken){
        if (is_object($usertoken) && method_exists($usertoken, 'bearerToken')) {
    
            $token = $usertoken->bearerToken();
        }else if (is_string($usertoken)) {
    
            $token = $usertoken;
        }else {
   
    
        }

        $tokenParts = explode('|', $token);
        if (count($tokenParts) !== 2) {
            return response()->json(['error' => 'Invalid token format'], 400);
        }

        $tokenId = $tokenParts[0]; // The ID part of the token
        $plainTextToken = $tokenParts[1]; // The plain text token part



        $user = User::whereHas('tokens', function ($query) use ($plainTextToken) {
            $query->where('token', hash('sha256', $plainTextToken)); 
        })->first();

        if (!$user) {
            return response()->json(['error' => 'User not found'], 404);
        }

        return response()->json(["id" =>$user->id]);
    }*/

 

    private function UserAppointments($userId){
        $tableName = $userId . "_app";
        $query = "CREATE TABLE `$tableName` (
            id INT(20) NOT NULL AUTO_INCREMENT PRIMARY KEY,
            owner_id VARCHAR(220) NOT NULL DEFAULT '$userId',
            app_id VARCHAR(220) NOT NULL ,
            app_user_id VARCHAR(220) NOT NULL,
            time_from DATETIME(6) NOT NULL,
            time_to DATETIME(6) NOT NULL,
            title VARCHAR(255) NOT NULL,
            description TEXT NOT NULL
           
        )";

        try{
            DB::statement($query);
            return true;
        }catch(Throwable $e){
            return false;
        }


        


    }

    private function UserPeople($userId){
         $people_table_name = $userId . "_people";
        $query = "CREATE TABLE `$people_table_name` (
            id INT(20) NOT NULL AUTO_INCREMENT PRIMARY KEY,
            owner_id VARCHAR(220) NOT NULL DEFAULT '$userId',
            f_status VARCHAR(220) NOT NULL ,
            user_f_id VARCHAR(220) NOT NULL
        )";
        try{
            DB::statement($query);
            return true;
        }catch(Throwable $e){
            return false;
        }
    }

    public function fillSettings($timezone ,$usertoken){
        $get_user_id = new getUserId;
        $userId_json = $get_user_id ->getId($usertoken);
        $userId = $userId_json;

        $user_id_exists = setting::where('owner_id',$userId)->exists();
        if($user_id_exists){
            $settings = setting::where('owner_id',$userId)->first();
            $settings -> time_zone = $timezone;
            
            try{
                $settings ->save();
                return true;
            }catch(Throwable $e){
                return false;

            }
        }else{
            $data = [
            'owner_id' => $userId,
            'work_from' => '09:00:00',
            'work_to' => '17:00:00',
            'break_time' => '00:15:00',
            'time_zone' => $timezone,
            'logic' => 'appointment',
            'max_app' => 10,
            'monday' => 'accepted',
            'tuesday' => 'accepted',
            'wednesday' => 'accepted',
            'thursday' => 'accepted',
            'friday' => 'accepted',
            'saturday' => 'rejected',
            'sunday' => 'rejected',
            'max_duration_swicth' => false, 
            'max_duration_time' => '00:00:00', 
            'min_time_switch' => false, 
            'min_time' => '00:00:00', 
            'app_fixed_duration_switch' => false, 
            'app_fixed_duration' => '00:00:00', 
            'allow_dm' => false
        ];
            try{
                setting::create ($data);
                return true;
            }catch(Throwable $e){
                return false;

            }


            
           

        }


    }
    private function UserFollower($userId){
        $data =[
            "userId" => $userId,
            "followers_num" => 0,
            "following_num" => 0
        ];
        $userFTable = new followers_user();
        $status = $userFTable::create($data);

        if ($status){
            return true;

        }else{
            return false;
        }

    }


}