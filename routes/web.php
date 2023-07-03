<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MainController;

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
    Route::get('/cvlist', 'cv');
    Route::get('/signin/checkuser', 'checkuser');
    Route::get('/register', 'signup');
    
    
});