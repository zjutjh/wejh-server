<?php

namespace App\Http\Controllers\Ycjw;

use Illuminate\Support\Facades\Auth;
use App\Models\Api;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class MainController extends Controller
{
    public function bind(Request $request) {
        if(!$user = Auth::user()) {
            return RJM(null, -1, '没有认证信息');
        }
        $password = $request->get('password');
        list($check, $errmsg) = $this->getCheck($user->uno, $password, null, true);
        if($check == false) {
            return RJM(null, -1, $errmsg);
        }
        $user->setExt('passwords.yc_password', encrypt($password));

        return RJM($user, 1, '绑定原创账号成功');
    }

    /**
     * 循环获取
     * @param $username
     * @param $password
     * @param null $port
     * @param bool $retry
     * @param int $timeout
     * @return array
     */
    public function getCheck($username, $password, $port = null, $retry = false, $timeout = 300) {
        $api = new Api;
        $check = $api->checkYcLogin($username, $password, $port, $timeout);
        if(!$check && !$retry) {
            if($api->getError() == '原创服务器错误') {
                return [false, '原创教务系统炸了'];
            }
            return [false, $api->getError()];
        }
        if(!$check && $retry) {
            for ($i = 83; $i <= 86; $i++) {
                $check = $api->checkYcLogin($username, $password, $i, $timeout);
                if($check) {
                    break;
                }
            }
            if(!$check) {
                return [false, $api->getError()];
            }
        }
        return [$check, $api->getError()];
    }
}
