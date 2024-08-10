<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Mail\signin_verification_email_code;
use Mail;
use App\Http\Controllers\API\Session_controler;




class signin_verification_code extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        dd("qwe");
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
    public function store(Request $request)
    {
        //
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
    public function sendEmail( $user_data){
        //dd("hi");
       
        $very_code = rand(1000,9999);
        $data = [
            "key" => "very_code",
            "data" => "$very_code"

        ];
        $session_req = new Session_controler;
        $session_result = $session_req -> session_selector("put" , $data);
        
       

        $user_name = $user_data['username'];
        $user_email = $user_data['email'];

        
        $details = [
            'title' => "Verification Email: [company name] ",
            'body' => "Dear $user_name,

Thank you for registering with [Your Company/Website Name]. To complete your sign-in process, please use the following verification code:

Verification Code: [$very_code]

Please enter this code on the sign-in page to verify your email address. If you did not request this code, please disregard this email.

If you have any questions or need assistance, feel free to contact our support team.

Best regards",
'fname' => "$user_name",
'verificationCode' => "$very_code"

        ];
      
        Mail::to("$user_email")->send(new signin_verification_email_code($details) );
        return true ;
    }
}