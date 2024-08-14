<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\setting;

class SetUserTables extends Controller
{
    public function UserTableLucher(Request $usertoken){
        $userId_json = $this ->getId($usertoken);
        $userId = $userId_json->getData(true);
        
        $this -> UserAppointments($userId['id']);
        $this -> UserPeople($userId['id']);

    }
    
    private function getId($usertoken){
        $token = $usertoken -> bearerToken();
        $user = User::whereHas('tokens', function ($query) use ($token) {
            $query->where('token',  $token); 
        })->first();

        if (!$user) {
            return response()->json(['error' => 'User not found'], 404);
        }

        return response()->json(["id" =>$user->id]);
    }

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
        DB::statement($query);

    }

    private function UserPeople($userId){
         $people_table_name = $userId . "_people";
        $query = "CREATE TABLE `$people_table_name` (
            id INT(20) NOT NULL AUTO_INCREMENT PRIMARY KEY,
            owner_id VARCHAR(220) NOT NULL DEFAULT '$userId',
            f_status VARCHAR(220) NOT NULL ,
            user_f_id VARCHAR(220) NOT NULL
        )";
        DB::statement($query);
    }

    public function fillSettings($timezone ,$usertoken){

        $userId_json = $this ->getId($usertoken);
        $userId = $userId_json->getData(true);

        $user_id_exists = setting::where('owner_id',$userId)->exists();
        if($user_id_exists){
            $settings = setting::where('owner_id',$userId)->first();
            $settings -> time_zone = $timezone;
            $settings ->save();

        }else{
            $data = [
                'owner_id' => $userId['id'],
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
                'sunday' => 'rejected'
            ];

            
           setting::create ($data);

        }


    }


}