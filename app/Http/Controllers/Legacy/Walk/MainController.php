<?php

namespace App\Http\Controllers\Legacy\Walk;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class MainController extends Controller
{
    public function main(Request $request) {
        return view('walk');
    }

    public function search(Request $request) {
        $name = $request->get('name');
        $iid = $request->get('iid');
        $result = http_post('http://api.lyx.name/walk_group', [
            'name' => $name,
            'idcard' => $iid,
        ]);
        return response($result)->header('Content-Type', 'application/json');
    }
}
