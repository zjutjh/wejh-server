<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Api;
use Illuminate\Support\Facades\Request;

class ResetPasswordController extends Controller
{
    public function forgot(Request $request) {
        $username = $request->get('username');
        $password = $request->get('password');
        $iid = $request->get('iid');
        $api = new Api;
        if(!$api->resetJhPassport($username, $password, $iid)) {
            return RJM(null, -1, $api->getError());
        }
        return RJM(null, 1, '重置密码成功');
    }
}
