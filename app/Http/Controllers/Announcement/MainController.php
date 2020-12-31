<?php

namespace App\Http\Controllers\Announcement;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\AnnouncementService;

class MainController extends Controller
{
    public function api(Request $request) {
        $res = AnnouncementService::getAnnouncement();
        return RJM($res, 1, 'ok');
    }
}
