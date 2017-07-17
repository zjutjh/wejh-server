<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
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

Route::any('/wechat',  [
    'uses' => 'Wechat\ServerController@serve']);

Route::get('/login',  [
    'uses' => 'Auth\LoginController@authenticate'])->middleware('web');

Route::get('/user',  [
    'uses' => 'Auth\LoginController@getAuthenticatedUser'])->middleware('web', 'jwt.api.auth');

Route::group(['middleware' => ['web', 'cors']], function () {
    Route::get('/signpackage', [
        'uses' => 'Wechat\JSSDKController@signPackage']);
    Route::get('/tip', [
        'uses' => 'Tip\MainController@tip']);
    Route::get('/banner', [
        'uses' => 'Banner\MainController@banner']);
    Route::get('/time', [
        'uses' => 'Ycjw\TimeController@api']);
    Route::get('/user', [
        'uses' => 'Auth\LoginController@user']);
    Route::patch('/user', [
        'uses' => 'Auth\LoginController@update']);
    Route::post('/login', [
        'uses' => 'Auth\LoginController@login']);
    Route::post('/autoLogin', [
        'uses' => 'Auth\LoginController@autoLogin']);
    Route::post('/ycjw/bind', [
        'uses' => 'Ycjw\MainController@bind']);
    Route::get('/ycjw/score', [
        'uses' => 'Ycjw\ScoreController@score']);
    Route::patch('/ycjw/score', [
        'uses' => 'Ycjw\ScoreController@update']);
    Route::get('/ycjw/timetable', [
        'uses' => 'Ycjw\TimetableController@timetable']);
    Route::patch('/ycjw/timetable', [
        'uses' => 'Ycjw\TimetableController@update']);
    Route::get('/ycjw/exam', [
        'uses' => 'Ycjw\ExamController@exam']);
    Route::patch('/ycjw/exam', [
        'uses' => 'Ycjw\ExamController@update']);
    Route::post('/card/bind', [
        'uses' => 'Card\LoginController@bind']);
    Route::get('/card', [
        'uses' => 'Card\MainController@card']);
    Route::post('/library/bind', [
        'uses' => 'Library\LoginController@bind']);
    Route::get('/library/borrow', [
        'uses' => 'Library\MainController@borrow']);
    Route::post('/register', [
        'uses' => 'Auth\RegisterController@active']);
    Route::post('/forgot', [
        'uses' => 'Auth\ResetPasswordController@forgot']);
    Route::post('/freeroom', [
        'uses' => 'Ycjw\FreeroomController@api']);
});