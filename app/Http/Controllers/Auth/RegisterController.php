<?php

namespace App\Http\Controllers\Auth;

use App\Models\Api;
use App\Models\User;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Request;

class RegisterController extends Controller
{
    public function active(Request $request) {
        $username = $request->get('username');
        $password = $request->get('password');
        $iid = $request->get('iid');
        $email = $request->get('email');
        $api = new Api;
        if(!$api->activeJhPassport($username, $password, $iid, $email)) {
            return RJM(null, -1, $api->getError());
        }
        return RJM(null, 1, '激活成功');
    }
}
