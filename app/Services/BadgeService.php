<?php
namespace App\Services;

class BadgeService {

    public static function getBadges()
    {
        $badges = [
            '/home/feedback',
            '/index/freeroom/moganshan',
            '/index/bus-weblink',
            '/home/profile_v1',
        ];
        return [
            'allBadges' => $badges
        ];
    }

}
