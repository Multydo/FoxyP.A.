<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\http\Controllers\API\UserController;
use App\Models\Otp;
use Illuminate\Support\Facades\Hash;

/**
 * @OA\Tag(
 *     name="Profile",
 *     description="Operations related to user profiles"
 * )
 */

class ProfileController extends Controller
{
    /**
 * @OA\Post(
 *     path="/api/profile/getinfo",
 *     summary="Get the user's profile",
 *     description="Retrieves the profile information of the authenticated user.",
 *     operationId="getProfile",
 *     tags={"Profile"},
 *     security={{"bearerAuth": {}}},
 *     @OA\Response(
 *         response=200,
 *         description="User data found",
 *         @OA\JsonContent(
 *             @OA\Property(property="message", type="string", example="user data found"),
 *             @OA\Property(
 *                 property="data",
 *                 type="object",
 *                 @OA\Property(property="fname", type="string", example="John"),
 *                 @OA\Property(property="lname", type="string", example="Doe"),
 *                 @OA\Property(property="username", type="string", example="johndoe"),
 *                 @OA\Property(property="email", type="string", example="john@example.com")
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=401,
 *         description="Unauthorized",
 *         @OA\JsonContent(
 *             @OA\Property(property="message", type="string", example="Unauthorized")
 *         )
 *     )
 * )
 */

    public function getProfile(Request $request){
        $userCon = new UserController;
        $token = $request->bearerToken();
        $userId = $userCon->getId($token);

        $userInfo = User::where("id",$userId)
            ->select("fname","lname","username","email")->first();
        return response()->json([
            "message"=>"user data found",
            "data"=>$userInfo
        ],200);
    }
    /**
     * @OA\Post(
     *     path="/api/profile/changename",
     *     summary="Change the user's name",
     *     description="Updates the first and last name of the authenticated user.",
     *     operationId="changeName",
     *     tags={"Profile"},
     *     security={{"bearerAuth": {}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"fname", "lname"},
     *             @OA\Property(property="fname", type="string", description="First name", example="John"),
     *             @OA\Property(property="lname", type="string", description="Last name", example="Doe")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="User name saved",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="user name saved")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Unauthorized")
     *         )
     *     )
     * )
     */
   

    
    public function changeName(Request $request){
        $userCon = new UserController;
        $token = $request->bearerToken();
        $userId = $userCon->getId($token);

        $userInfo = User::where("id",$userId)->first();
        $userInfo->fname = $request->fname;
        $userInfo->lname = $request->lname;
        $userInfo->save();

        return response()->json([
            "message"=>"user name saves"

        ],200);
    }
    /**
     * @OA\Post(
     *     path="/api/profile/changeemail",
     *     summary="Change the user's email",
     *     description="Updates the email address of the authenticated user and sends a verification code.",
     *     operationId="changeEmail",
     *     tags={"Profile"},
     *     security={{"bearerAuth": {}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"email"},
     *             @OA\Property(property="email", type="string", format="email", description="New email address", example="newemail@example.com")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Email saved, verification needed",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="user email saved and need verification")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Email already in use",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="email already in use")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal server error",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="internal server error (problem in sending otp email)")
     *         )
     *     )
     * )
     */
    public function changeEmail(Request $request){
        $userCon = new UserController;
        $token = $request->bearerToken();
        $userId = $userCon->getId($token);
        $newEmail = $request->email;
        

        $userInfo = User::where("id",$userId)->first();
        $state = User::where("email",$newEmail)->first();
        if($state){
            return response()->json([
                "message"=>"email alresy in use"
            ],401);
        }else{
            $userInfo->temp_email = $request->email;
            
            $userInfo->save();

            $username = User::where("id",$userId)->select("username")->first();
            $user_data = [
                "email"=>$newEmail,
                "username"=>$username["username"]
            ];

            $stateOtp = $userCon->sendEmail($user_data);
            if($stateOtp){
                return response()->json([
                    "message"=>"user email saved and need verification"
                    ],200);
            }else{
                return response()->json([
                    "message"=>"internal server error (problem in sending otp email)"
                ],500);
            }

            
             
        }
        
    }

    /**
     * @OA\Post(
     *     path="/api/profile/verifynewemail",
     *     summary="Verify the new email address",
     *     description="Verifies the new email address using the provided verification code.",
     *     operationId="verifyNewEmail",
     *     tags={"Profile"},
     *     security={{"bearerAuth": {}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"code"},
     *             @OA\Property(property="code", type="string", description="Verification code", example="123456")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Email verified",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="user new email is set to start using it")
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Verification code does not match",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="verification code does not match")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Unauthorized")
     *         )
     *     )
     * )
     */

    public function verifyNewEmail(Request $request){
        $user_side_verification_code = $request["code"];
        //dd($user_side_verification_code);
        $token = $request->bearerToken();
        $userCon = new UserController;
        $user_id = $userCon->getId($token);

        $userInfo = User::find($user_id);
        
            $vcode = Otp::where("email",$userInfo->temp_email)->select("code")->orderBy("id", "desc")->first();
        //dd($vcode["code"]);
        if($vcode["code"] == $user_side_verification_code){
            
            $userInfo->email = $userInfo -> temp_email;
            $userInfo->save();
            Otp::where("email", $userInfo["temp_email"])->delete();
            return response()->json([
                "message"=>"user new email is set to sart using it"
            ],201);

            
        }else{
            Otp::where("email", $userInfo["temp_email"])->delete();
            return response()->json([
                "message"=>"verification code does not match"
            ],400);
        }   
    }
    /**
     * @OA\Post(
     *     path="/api/profile/changepass",
     *     summary="Change the user's password",
     *     description="Updates the password of the authenticated user.",
     *     operationId="changePassword",
     *     tags={"Profile"},
     *     security={{"bearerAuth": {}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"old_password", "new_pass_1", "new_pass_2"},
     *             @OA\Property(property="old_password", type="string", format="password", description="Old password", example="oldpass123"),
     *             @OA\Property(property="new_pass_1", type="string", format="password", description="New password", example="newpass123"),
     *             @OA\Property(property="new_pass_2", type="string", format="password", description="Confirm new password", example="newpass123")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Password changed",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="new pass is set")
     *         )
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="New passwords do not match",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="the new pass does not match")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Old password does not match",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="old pass does not match")
     *         )
     *     )
     * )
     */

    public function changePassword(Request $request){
        $userCon = new UserController;
        $token = $request->bearerToken();
        $userId = $userCon->getId($token);

        $userInfo = User::find($userId);
        if(Hash::check($request->old_password, $userInfo->password)){
            if($request->new_pass_1 == $request->new_pass_2){
                $userInfo->password  = $request->new_pass_1;
                $userInfo->save();
                return response()->json([
                    "message"=>"new pass is set"
                ],201);
            }else{
                return response()->json([
                    "message"=>"the new pass does not match"
                ],403);
            }
        }else{
            return response()->json([
                "message"=>"old pass does not match"
            ],401);
        }
    }
}