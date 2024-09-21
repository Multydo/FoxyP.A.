<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\UserController;
use App\Http\Controllers\API\signin_verification_code;

use App\Http\Controllers\API\TimeController;
use App\Http\Controllers\API\HomeController;
use App\Http\Controllers\API\SettingsController;
use App\Http\Controllers\API\PeopleController;
use App\Http\Controllers\API\RequestController;
use App\Http\Controllers\API\ProfileController;


// testing files links
use App\Http\Controllers\API\testGetInfo;
use App\Http\Controllers\API\DynamicTableController;



/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/
Route::get('/', function () {
    return 'welcome123123';
});


// registering user
Route::post("/register_user",[UserController::class,"register"]);
Route::post("/verify",[UserController::class,"verify"]);

//forgot pass 
Route::middleware('api')->post("/forgotpass",[UserController::class,"forgotPasswordCode"]);
Route::middleware('api')->post("/forgotpass/submit",[UserController::class,"forgoPassword"]);

//manual login
Route::post('/login_user',[UserController::class , "login"] );

//home page and auto login
Route::middleware('api')->post('/home',[HomeController::class,"master"]);
Route::middleware('api')->post("/home/showDetails",[HomeController::class,"showDetails"]);


//profile page
Route::middleware('api')->post("/profile/getinfo",[ProfileController::class,"getProfile"]);
Route::middleware('api')->post("/profile/changename",[ProfileController::class,"changeName"]);
Route::middleware('api')->post("/profile/changeemail",[ProfileController::class,"changeEmail"]);
Route::middleware('api')->post("/profile/verifynewemail",[ProfileController::class,"verifyNewEmail"]);
Route::middleware('api')->post("/profile/changepass",[ProfileController::class,"changePassword"]);

//settings page
Route::middleware('api')->post('/settings/getSettings',[SettingsController::class,"getsettings"]);
Route::middleware('api')->post('/settings/saveSettings', [SettingsController::class, 'saveSettings']);

//people page
Route::middleware('api')->post('/people/getPeople',[PeopleController::class,'getFollowing']);
Route::middleware('api')->post('/people/followUser',[PeopleController::class,'followPerson']);
Route::middleware('api')->post('/people/unfollowPeople',[PeopleController::class,'unfollowPeople']);
Route::middleware('api')->post('/people/searchPeople',[PeopleController::class,'searchPeople']);

//request page
Route::middleware('api')->post('/requests/setrequest' ,[RequestController::class,"setrequest"]);
Route::middleware('api')->post("/requests/checkDate",[RequestController::class,"checkDateAvailability"]);
Route::middleware('api')->post("/requests/sendRequest",[RequestController::class,"sendRequest"]);

//profile page 
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'getProfile']);
    Route::post('/profile', [ProfileController::class, 'updateProfile']);});

//testing links not allowed in production




Route::get('/store',[UserController::class , "store"]);
Route::post('/getUserInfo',[testGetInfo::class , "getUserInfo"]);
Route::post('/testing',[DynamicTableController::class , "UserTableLucher"]);