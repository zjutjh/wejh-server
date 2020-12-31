<?php

namespace App\Http\Controllers\Bootstrap;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\AppListService;
use App\Services\AnnouncementService;
use App\Services\BadgeService;
use App\Services\TermTimeService;

class MainController extends Controller
{
    public function getBootstrapInfo(Request $request) {
        $announcements = AnnouncementService::getAnnouncement();
        $appList = AppListService::getAppList();
        $termTime = TermTimeService::getTermTime();
        $badges = BadgeService::getBadges();
        $res = [
            'announcement' => $announcements,
            'appList' => $appList,
            'badges' => $badges,
            'termTime' => $termTime
        ];
        return RJM($res, 1, 'ok');
    }
}
