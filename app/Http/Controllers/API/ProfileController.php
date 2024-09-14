<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

class ProfileController extends Controller
{
    public function getProfile()
    {
        // Return user data except password
        return response()->json(Auth::user()->only('fname', 'lname', 'username', 'email'));
    }

    public function updateProfile(Request $request)
    {
        $user = Auth::user();

        // Update fname and lname without restrictions
        $user->fname = $request->fname;
        $user->lname = $request->lname;

        // Username cannot be changed

        // Email update with verification process
        if ($request->email !== $user->email) {
            $user->email = $request->email;
            $user->verification_status = false;

            // Send OTP email for verification
            $otp = rand(100000, 999999); // Generate OTP
            Mail::to($user->email)->send(new OTPMail($otp));

            // Save OTP to the session
            session(['otp' => $otp]);
        }

        // Password change with verification via OTP
        if ($request->password) {
            $otp = session('otp');
            if ($request->otp === $otp) {
                $user->password = Hash::make($request->password);
                session()->forget('otp'); // Clear OTP after use
            } else {
                return response()->json(['error' => 'Invalid OTP'], 400);
            }
        }

        $user->save();

        return response()->json(['success' => 'Profile updated successfully']);
    }
}
