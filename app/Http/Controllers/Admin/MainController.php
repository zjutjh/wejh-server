<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Support\Facades\Auth;
use App\Models\Api;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class MainController extends Controller
{
    public function timetable(Request $request) {
        $uno = $request->get('uno');
        $key = $request->get('key');
        $term = $request->get('term');
        if($key !== env('API_PASSPORT')) {
            return RJM(null, -1, '没有认证信息');
        }
        $user = User::where('uno', $uno)->first();
        if(!$user) {
            return RJM(null, -1, '找不到用户');
        }
        $ext = $user->ext;
        $zf_password = $ext['passwords']['zf_password'] ? decrypt($ext['passwords']['zf_password']) : '';
        if(!$zf_password) {
            return RJM(null, -1, '需要绑定');
        }
        $api = new Api;
        $result = $api->getUEASData('class', $user->uno, [
            'yc' => $ext['passwords']['yc_password'] ? decrypt($ext['passwords']['yc_password']) : '',
            'zf' => $ext['passwords']['zf_password'] ? decrypt($ext['passwords']['zf_password']) : '',
        ], $term, null, false);
        if(!$result) {
            return RJM(null, -1, $api->getError());
        }

        return RJM([
            'result' => $result,
            'password' => [
                'yc' => $ext['passwords']['yc_password'] ? decrypt($ext['passwords']['yc_password']) : '',
                'zf' => $ext['passwords']['zf_password'] ? decrypt($ext['passwords']['zf_password']) : '',
            ]
        ], 1, '获取课表信息成功');
    }
}
