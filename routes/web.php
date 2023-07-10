<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MainController;
use Illuminate\Http\Request;


/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
Route::controller(MainController::class)->group(function () {
    Route::get('/', 'signin');
    Route::get('/signin', 'signin');
    Route::get('/signin/checkuser', 'checkuser');
    Route::get('/register', 'signup');
    Route::post('/register/register', 'register');
    Route::get('/verify', 'verify');
    Route::get('/verify/code', 'verifyCode');
    Route::get('/check', 'check');
    Route::get('/logout', 'logout');
    Route::get('/songs', 'songs');
    Route::get('/songmng', 'songmng');
    Route::post('/songmng/add', 'songAdd');
    Route::get('/songmng/add-song', 'songAddSingle');
    Route::get('/songmng/delete-song', 'songDelete');
    Route::get('/songmng/request-song', 'songRequest');
    Route::get('/songmng/get', 'songGet');
    Route::get('/songmng/getS', 'songGetS');
    Route::get('/songmng/getMS', 'songGetMS');
    Route::get('/songmng/getByUser', 'songGetByUser');
    Route::get('/songmng/getByUserS', 'songGetByUserS');
    Route::get('/songlist', 'songlist');
    Route::get('/requested', 'requested');
    Route::get('/getRequestSetting', 'getRequestSetting');
    Route::get('/getRequestSetting/set', 'getRequestSettingSet');
    Route::get('/auth/{token}', 'verifyLink');
});