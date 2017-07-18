<?php

namespace App\Http\Controllers\Library;

use Illuminate\Support\Facades\Auth;
use App\Models\Api;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class MainController extends Controller
{
    public function borrow(Request $request) {
        if(!$user = Auth::user()) {
            return RJM(null, -1, '没有认证信息');
        }
        $ext = $user->ext;
        $lib_password = $ext['passwords']['lib_password'] ? decrypt($ext['passwords']['lib_password']) : '';
        if(!$lib_password) {
            $lib_password = $user->uno;
        }
        $api = new Api;
        $result = $api->getBookBorrow($user->uno, $lib_password);
        if(!$result) {
            return RJM(null, -1, $api->getError());
        }
        return RJM($result, 1, '获取借阅信息成功');
    }

    public function bind(Request $request) {
        if(!$user = Auth::user()) {
            return RJM(null, -1, '没有认证信息');
        }
        $password = $request->get('password');
        $api = new Api;
        $check = $api->getBookBorrow($user->uno, $password);
        if(!$check) {
            return RJM(null, -1, '用户名或密码错误');
        }
        $user->setExt('passwords.lib_password', encrypt($password));

        return RJM($user, 1, '绑定图书馆账号成功');
    }
}
