<?php

namespace App\Http\Controllers\Library;

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
        return RJM($result, 1, '登陆成功');
    }
}
