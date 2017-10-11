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

Route::any('/git/pull',  function (Request $request) {
    Artisan::call('git:pull');
});

Route::get('/user',  [
    'uses' => 'Auth\LoginController@getAuthenticatedUser'])->middleware('web', 'jwt.api.auth');

Route::group(['middleware' => ['web']], function () {
    Route::get('/signpackage', [
        'uses' => 'Wechat\JSSDKController@signPackage']);

    Route::get('/tip', [
        'uses' => 'Tip\MainController@tip']);

    Route::get('/announcement', [
        'uses' => 'Announcement\MainController@api']);

    Route::get('/app-list', [
        'uses' => 'ApplicationsList\MainController@applicationsList']); // 微精弘首页应用列表

    Route::post('/login', [
        'uses' => 'Auth\LoginController@login']);

    Route::post('/register', [
        'uses' => 'Auth\RegisterController@active']);

    Route::post('/forgot', [
        'uses' => 'Auth\ResetPasswordController@forgot']);

    Route::post('/code/weapp', [
        'uses' => 'Auth\OauthController@weapp']);

    Route::post('/autoLogin', [
        'uses' => 'Auth\LoginController@autoLogin']);

    Route::get('/time', [
        'uses' => 'Ycjw\TimeController@api']);

    Route::group(['middleware' => ['jwt.api.auth']], function () {
        Route::get('/banner', [
            'uses' => 'Banner\MainController@banner']);
        Route::get('/user', [
            'uses' => 'Auth\LoginController@user']);
        Route::PUT('/user', [
            'uses' => 'Auth\LoginController@update']);
        Route::post('/ycjw/bind', [
            'uses' => 'Ycjw\MainController@bind']);
        Route::post('/zf/bind', [
            'uses' => 'Ycjw\MainController@bindZf']);
        Route::get('/ycjw/score', [
            'uses' => 'Ycjw\ScoreController@score']);
        Route::PUT('/ycjw/score', [
            'uses' => 'Ycjw\ScoreController@update']);
        Route::get('/ycjw/timetable', [
            'uses' => 'Ycjw\TimetableController@timetable']);
        Route::PUT('/ycjw/timetable', [
            'uses' => 'Ycjw\TimetableController@update']);
        Route::get('/ycjw/exam', [
            'uses' => 'Ycjw\ExamController@exam']);
        Route::PUT('/ycjw/exam', [
            'uses' => 'Ycjw\ExamController@update']);
        Route::post('/card/bind', [
            'uses' => 'Card\MainController@bind']);
        Route::get('/card', [
            'uses' => 'Card\MainController@card']);
        Route::post('/library/bind', [
            'uses' => 'Library\MainController@bind']);
        Route::get('/library/borrow', [
            'uses' => 'Library\MainController@borrow']);
        Route::get('/freeroom', [
            'uses' => 'Ycjw\FreeroomController@api']);
        Route::get('/teacher', [
            'uses' => 'Teacher\MainController@search']);
    });
});