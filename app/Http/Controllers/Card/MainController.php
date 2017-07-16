<?php

namespace App\Http\Controllers\Card;

use Illuminate\Support\Facades\Auth;
use App\Models\Api;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class MainController extends Controller
{
    public function card(Request $request) {
        if(!$user = Auth::user()) {
            return RJM(null, -1, '没有认证信息');
        }
        $ext = $user->ext;
        $card_password = $ext['passwords']['card_password'] ? decrypt($ext['passwords']['card_password']) : '';
        if(!$card_password) {
            return RJM(null, -1, '需要绑定');
        }
        $api = new Api;
        $result = $api->getCardBalance($user->uno, $card_password);
        if(!$result) {
            return RJM(null, -1, $api->getError());
        }

        return RJM($result, 1, '获取一卡通信息成功');
    }

    public function bind(Request $request) {
        if(!$user = Auth::user()) {
            return RJM(null, -1, '没有认证信息');
        }
        $password = $request->get('password');
        $api = new Api;
        $check = $api->getCardBalance($user['uno'], $password);
        if(!$check) {
            return RJM(null, -1, '用户名或密码错误');
        }
        $user->setExt('passwords.card_password', encrypt($password));

        return RJM($user, 1, '绑定成功');
    }
}
