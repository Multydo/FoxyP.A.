<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class Session_controler extends Controller
{
    public function session_selector($protocole , $session_req_data){
        
        switch($protocole){
            case "put" :
                return $this->session_put($session_req_data);
                break;
            case "get":
                return $this->session_get($session_req_data);
                break;
            case "flush":
                return $this ->session_flush();
                break;
        }

    }

    public function session_put($session_req_data){
        $key = $session_req_data['key'];
        $data =$session_req_data['data'];
        Session::put($key, $data);
        return true;
       
    }

    public function session_get($session_req_data){
        $key = $session_req_data['key'];
        $data = Session::get($key);
        return $data;

       
    }
       private function session_flush()
    {
        Session::flush();
        return true ;
    }

}