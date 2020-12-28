<?php
namespace App\Services;

class BadgeService {

    public static function getBadges()
    {
        $badges = [
            '/home/feedback',
            '/index/freeroom/moganshan',
            '/index/questionnaire',
        ];
        return [
            'allBadges' => $badges
        ];
    }

}
