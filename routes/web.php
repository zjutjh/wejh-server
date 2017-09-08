<?php
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
use Illuminate\Support\Facades\Redis;

Route::get('/',  function () {
    return response('微精弘');
});
Route::get('/oauth/wechat', [
    'uses' => 'Auth\OauthController@wechat']);
Route::get('/oauth/wechat/login', [
    'uses' => 'Auth\OauthController@wechatLogin']);

// 微信服务号/订阅号名片跳转
Route::get('/weixincard/{id}',  function ($id) {
    return redirect('https://mp.weixin.qq.com/mp/profile_ext?action=home&scene=110&__biz='.$id.'==#wechat_redirect');
});
