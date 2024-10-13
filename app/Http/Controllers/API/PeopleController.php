<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Controllers\API\UserController;
use Illuminate\Support\Facades\DB;
use Throwable;
/**
 * @OA\Tag(
 *     name="People",
 *     description="Operations related to people (followers, following, etc.)"
 * )
 */

class PeopleController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/people/getPeople",
     *     summary="Get list of users the current user is following",
     *     description="Returns a list of users that the authenticated user is following.",
     *     operationId="getFollowing",
     *     tags={"People"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="users found"),
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 @OA\Items(
     *                     type="object",
     *                     @OA\Property(property="fname", type="string", example="John"),
     *                     @OA\Property(property="lname", type="string", example="Doe"),
     *                     @OA\Property(property="id", type="integer", example=123)
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="user not found or token is broken")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal server error",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Error message")
     *         )
     *     )
     * )
     */
    public function getFollowing(Request $request){
        $getId = new UserController;
        $userId= $getId-> getId($request);

        if($userId){

            $userTable = $userId . "_people";

            try{$follow_data = DB::table('users as u')
                    ->join($userTable . ' as f', 'u.id', '=', 'f.user_f_id')
                    ->where('f_status', 'following')
                    ->select('u.fname', 'u.lname', 'u.id')
                    ->get()
                    ->toArray();
            }catch(Throwable $e){
                return response()->json([
                    "message"=> $e->getMessage()
                ],500);
            }
            

            $status = true;
            $text = "users fownd";                

            if (empty($follow_data)) {
                $follow_data[] = '0';
                $status = false;
                $text = "no users fownd";  
            }
            return response()->json([
                "message"=>$text,
                "status"=>$status,
                "data" => $follow_data
            ],200);



        }else{
            return response()->json([
                "message"=>"user not found or token is broken"

            ],401);
        }
    }
    /**
     * @OA\Post(
     *     path="/api/people/followUser",
     *     summary="Follow a user",
     *     description="Allows the authenticated user to follow another user.",
     *     operationId="followPerson",
     *     tags={"People"},
     *     security={{"bearerAuth": {}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"other_user"},
     *             @OA\Property(
     *                 property="other_user",
     *                 type="integer",
     *                 description="ID of the user to follow",
     *                 example=456
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="User followed successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="user followed successfully")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="user not found or token is broken")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal server error",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Error message")
     *         )
     *     )
     * )
     */
    public function followPerson(Request $request){
        $token = $request -> bearerToken();
        $get_user_id = new UserController;
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

       
       

        try{
            $result21 = DB::table('followers_users')
                ->where('userId', $userId)
                ->increment('following_num', 1);
        }catch(Throwable $e){
            return response()->json([
                "message"=> $e->getMessage()
            ],500);
        }
        $f_status3 = "follower";
        try{DB::table($userFTable)->insert([
            'owner_id' => $userFId,
            'f_status' => $f_status3,
            'user_f_id' => $userId,
        ]);}catch(Throwable $e){
            return response()->json([
                "message"=> $e->getMessage()
            ],500);}

    
        
        

        try{
            $result31 = DB::table('followers_users')
                ->where('userId', $userFId)
                ->increment('followers_num', 1);
        }catch(Throwable $e){
            return response()->json([
                "message"=> $e->getMessage()
            ],500);
        }

        
        return response()->json([
            "message"=>"userd followed sucsesfully"
        ],200);
        
    }
    /**
     * @OA\Post(
     *     path="/api/people/unfollowPeople",
     *     summary="Unfollow a user",
     *     description="Allows the authenticated user to unfollow another user.",
     *     operationId="unfollowPeople",
     *     tags={"People"},
     *     security={{"bearerAuth": {}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"other_user"},
     *             @OA\Property(
     *                 property="other_user",
     *                 type="integer",
     *                 description="ID of the user to unfollow",
     *                 example=456
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="User unfollowed successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="user unfollowed")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="user not found or token is broken")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal server error",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Error message")
     *         )
     *     )
     * )
     */

    public function unfollowPeople(Request $request){
        $token = $request -> bearerToken();
        $get_user_id = new UserController;
        $userId = $get_user_id -> getId($token);
        $requestData = $request->json()->all();
        $userFId = $requestData["other_user"];

        $userTable = $userId."_people";
        $userFTable = $userFId."_people";

        try{
            DB::table($userTable)->where('user_f_id', $userFId)->delete();
            DB::table($userFTable)->where('user_f_id', $userId)->delete();
        }catch(Throwable $e){
            return response()->json([
                "message"=> $e->getMessage()
            ],500);
        }

        

        try{
            $num_row_p = DB::table($userTable)->count();
            $num_row_f_p = DB::table($userFTable)->count();
        }catch(Throwable $e){
            return response()->json([
                "message"=> $e->getMessage()
            ],500);
        }
        

         try{
            DB::table('followers_users')
                ->where('userId', $userId)
                ->update(['following_num' => $num_row_p]);
        }catch(Throwable $e){
            return response()->json([
                "message"=> $e->getMessage()
            ],500);
        }
       

         try{
            DB::table('followers_users')
                ->where('userId', $userFId)
                ->update(['following_num' => $num_row_f_p]);
        }catch(Throwable $e){
            return response()->json([
                "message"=> $e->getMessage()
            ],500);
        }
        return response()->json([
            "message"=>"user unfollowed"

        ],200);
    }
    /**
     * @OA\Post(
     *     path="/api/people/searchPeople",
     *     summary="Search for people",
     *     description="Allows the authenticated user to search for other users.",
     *     operationId="searchPeople",
     *     tags={"People"},
     *     security={{"bearerAuth": {}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"search"},
     *             @OA\Property(
     *                 property="search",
     *                 type="string",
     *                 description="Search term",
     *                 example="John"
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Search results",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 @OA\Items(
     *                     type="object",
     *                     @OA\Property(property="fname", type="string", example="John"),
     *                     @OA\Property(property="lname", type="string", example="Doe"),
     *                     @OA\Property(property="f_status", type="string", example="following"),
     *                     @OA\Property(property="id", type="integer", example=123)
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="user not found or token is broken")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal server error",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Error message")
     *         )
     *     )
     * )
     */

    public function searchPeople(Request $request){
        $token = $request -> bearerToken();
        $get_user_id = new UserController;
        $userId = $get_user_id -> getId($token);
        $requestData = $request->json()->all();
        
        $userTable = $userId."_people";
        $gues = $requestData['search'];
        try{
            $search_data = DB::table('users as u')
                //->join('followers_users as f', 'u.id', '=', 'f.userId')
                ->leftJoin($userTable . ' as p', 'p.user_f_id', '=', 'u.id')
                ->select( 'u.fname', 'u.lname', 'p.f_status','u.id')
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
        }catch(Throwable $e){
            return response()->json([
                "message"=> $e->getMessage()
            ],500);
        }
        

        // Check if there are results
        if (empty($search_data)) {
            $search_data[] = '0';
        }

        return response()->json([$search_data]);
    }
}