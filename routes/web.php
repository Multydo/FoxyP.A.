<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\signin_controler;
use App\Http\Controllers\API\signin_verification_code;
use App\Http\Controllers\API\LoginControler;
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



//testing links not allowed in production


Route::post('/verifying',[signin_controler::class , "check_code"]);
Route::get('/store',[signin_controler::class , "store"]);