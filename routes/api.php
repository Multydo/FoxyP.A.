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



//manual login
Route::post('/login_user',[UserController::class , "login"] );

//home page and auto login
Route::post('/home',[HomeController::class,"master"]);

Route::post('/getSettings',[SettingsController::class,"getsettings"]);
Route::post('/saveSettings', [SettingsController::class, 'saveSettings']);
Route::post('/getPeople',[PeopleController::class,'getFollowing']);
Route::post('/followUser',[PeopleController::class,'followPerson']);
Route::post('/unfollowPeople',[PeopleController::class,'unfollowPeople']);
Route::post('/searchPeople',[PeopleController::class,'searchPeople']);

Route::post('/setrequest' ,[RequestController::class,"setrequest"]);
Route::post("/checkDate",[RequestController::class,"checkDateAvailability"]);
Route::post("/sendRequest",[RequestController::class,"sendRequest"]);

//profile page 
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'getProfile']);
    Route::post('/profile', [ProfileController::class, 'updateProfile']);});

//testing links not allowed in production

Route::post("/showDetails",[HomeController::class,"showDetails"]);


Route::get('/store',[UserController::class , "store"]);
Route::post('/getUserInfo',[testGetInfo::class , "getUserInfo"]);
Route::post('/testing',[DynamicTableController::class , "UserTableLucher"]);