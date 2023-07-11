<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\MainAppController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::controller(MainAppController::class)->group(function () {
    Route::get('/signin/checkuser', 'checkuser');
    Route::get('/verify/code', 'verifyCode');
    Route::get('/songmng/get', 'songGet');
    Route::get('/songmng/get-loadmore', 'songGetLoadMore');
    Route::get('/songmng/add', 'songAdd');
    Route::post('/songmng/upload-file', 'fileUpload');
    Route::get('/songmng/delete-song', 'songDelete');
    Route::get('/songmng/getCount', 'songGetCount');
    Route::get('/songmng/request-song', 'songRequest');
    Route::get('/songmng/getByUser', 'songGetByUser');
    Route::get('/songmng/getByUser-loadmore', 'songGetByUserLoadMore');
    Route::get('/songmng/getByUserCount', 'songGetByUserCount');
    Route::get('/getUserByPhone', 'getUserByPhone');
    Route::get('/getRequestSetting', 'getRequestSetting');
    Route::get('/getRequestSetting/set', 'getRequestSettingSet');
});