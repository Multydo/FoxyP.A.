<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\signin_controler;
use App\Http\Controllers\API\signin_verification_code;
use App\Http\Controllers\API\LoginControler;
use App\Http\Controllers\API\TimeZoneController;

// testing files links
use App\Http\Controllers\API\testGetInfo;
use App\Http\Controllers\API\SetUserTables;



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
    return 'welcome';
});

Route::post('/signin_user',[signin_controler::class , "start_verification"]);
Route::post('/login_user',[LoginControler::class , "login"] );
Route::post('/save_settings', [TimeZoneController::class, 'getTimeZone']);
Route::post('/build_tables',[SetUserTables::class , "UserTableLucher"]);


//testing links not allowed in production


Route::post('/verifying',[signin_controler::class , "check_code"]);
Route::get('/store',[signin_controler::class , "store"]);
Route::post('/getUserInfo',[testGetInfo::class , "getUserInfo"]);
Route::post('/testing',[SetUserTables::class , "UserTableLucher"]);