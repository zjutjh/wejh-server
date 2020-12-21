<?php

namespace App\Http\Controllers\ApplicationsList;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\AppListService;

class MainController extends Controller
{
    public function applicationsList(Request $request) {
        $res = AppListService::getAppList();
        return RJM($res, 1, 'ok');
    }
}
