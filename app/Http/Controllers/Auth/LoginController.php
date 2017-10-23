<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Api;
use App\Models\Student;
use App\Models\User;
use App\Models\UserLink;
use BadMethodCallException;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;

class LoginController extends Controller
{
    public function autoLogin(Request $request) {
        $type = $request->get('type'); // 第三方登录类型
        $openid = $request->get('openid'); // 第三方登录的用户标识
        if (!$openid) {
            return RJM(null, -401, '缺少用户标识');
        }

        if(!$user_link = UserLink::where('type', $type)->where('openid', $openid)->first()) {
            return RJM(null, -403, '自动登录失败');
        }
        $uid = $user_link->uid;
        Auth::loginUsingId($uid);

        try {
            // attempt to verify the credentials and create a token for the user
            if (!$token = JWTAuth::fromUser(Auth::user())) {
                return RJM(null, -401, '用户错误');
            }
        } catch (JWTException $e) {
            // something went wrong whilst attempting to encode the token
            return RJM(null, -500, 'token生成错误');
        }

        return RJM([
            'token' => $token,
            'user' => Auth::user()
        ], 200, '登陆成功');
    }
    /**
     * 登录逻辑，包括第三方登录
     * @param Request $request
     */
    public function login(Request $request) {
        $username = $request->get('username');
        $password = $request->get('password');
        $type = $request->get('type'); // 第三方登录类型
        $openid = $request->get('openid'); // 第三方登录的用户标识

        $api = new Api;
        if(!$check = $api->checkJhPassport($username, $password)) {
            $error = $api->getError();
            return RJM(null, -401, $error ? $error : '用户名或密码错误');
        }

        // 检测是否存在用户，不存在则创建
        if(!$user = User::where('uno', $username)->first()) {
            $user = new User;
            $user->uno = $username.'';
            $user->password = bcrypt($password);
            $ext = [];
            $ext['passwords']['jh_password'] = encrypt($password);
            $ext['passwords']['card_password'] = encrypt(substr($username,-6));
            $ext['passwords']['lib_password'] = encrypt($username);
            $school_info = Student::where('uno', $username)->first();
            $ext['school_info'] = $school_info;
            $user->ext = $ext;
            $user->save();
        }

        if ($type && $type != 'default') { // 如果是第三方登录，建立关联
            try {
                // 如果之前存在过关联，换成现在的
                if ($link = UserLink::where([
                    'openid' => $openid,
                    'type' => 'weapp',
                ])->first()) {
                    $link->delete();
                }
                $user = $this->$type($user, $openid); //反射到各个类型的方法
            } catch (BadMethodCallException $e) {
                return RJM(null, -500, '可能发生了一点错误，请联系管理员');
            }
        }

        try {
            // attempt to verify the credentials and create a token for the user
            if (! $token = JWTAuth::fromUser($user)) {
                return RJM(null, -401, 'token生成错误');
            }
        } catch (JWTException $e) {
            // something went wrong whilst attempting to encode the token
            return RJM(null, -500, 'token生成错误');
        }

        // all good so return the token
        return RJM([
            'user' => $user,
            'token' => $token
        ], 200, '登陆成功');
    }

    /**
     * 微信关联逻辑
     * @param Request $request
     */
    public function wechat($user, $openid) {
        $app = app('wechat');
        $userService = $app->user;
        $wechat_user = $userService->get($openid);
        $ext = $user->ext;
        $ext['wechat_info'] = $wechat_user;
        $user->ext = $ext;
        $user->save();

        // 建立连接
        $user_link = UserLink::firstOrNew([
            'uid' => $user->id,
            'type' => 'wechat',
        ]);
        $user_link->openid = $openid;
        $user_link->access_token = '';
        $user_link->save();

        return $user;
    }

    public function weapp($user, $openid) {
        // 建立连接
        $user_link = UserLink::firstOrNew([
            'uid' => $user->id,
            'type' => 'weapp',
        ]);
        $user_link->openid = $openid;
        $user_link->access_token = '';
        $user_link->save();

        return $user;
    }

    public function user(Request $request) {
        if(!$user = Auth::user()) {
            return RJM(null, -1, '没有认证信息');
        }

        return RJM($user, 200, '获取用户信息成功');
    }

    /**
     * 更新用户的个人信息，暂缓
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function update(Request $request) {
        if(!$user = Auth::user()) {
            return RJM(null, -1, '没有认证信息');
        }

        return RJM($user, 1, '更新成功');
    }
}
