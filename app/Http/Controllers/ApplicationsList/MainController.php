<?php

namespace App\Http\Controllers\ApplicationsList;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class MainController extends Controller
{
    public function applicationsList(Request $request) {
        $colors = ['red', 'green', 'blue', 'yellow', 'purple'];
        $icons = [
            'timetable' => [
                'bg' => 'blue',
                'icon' => cdn('img/app-list/timetable.png', false),
                'card' => cdn('img/card-background/timetable.png', false),
            ],
            'score' => [
                'bg' => 'red',
                'icon' => cdn('img/app-list/score.png', false),
            ],
            'exam' => [
                'bg' => 'green',
                'icon' => cdn('img/app-list/exam.png', false),
            ],
            'freeroom' => [
                'bg' => 'red',
                'icon' => cdn('img/app-list/freeroom.png', false),
            ],
            'student' => [
                'bg' => 'purple',
                'icon' => cdn('img/app-list/student.png', false),
            ],
            'teacher' => [
                'bg' => 'purple',
                'icon' => cdn('img/app-list/teacher.png', false),
            ],
            'card' => [
                'bg' => 'yellow',
                'icon' => cdn('img/app-list/card.png', false),
                'card' => cdn('img/card-background/card.png', false),
            ],
            'borrow' => [
                'bg' => 'blue',
                'icon' => cdn('img/app-list/borrow.png', false),
                'card' => cdn('img/card-background/borrow.png', false),
            ],
            'tri' => [
                'bg' => 'blue',
                'icon' => '',
                'card' => cdn('img/card-background/tri.png', false),
            ],
            'publicity' => [
                'bg' => array_random($colors),
                'icon' => cdn('img/app-list/publicity.png', false),
            ],
            'square' => [
                'bg' => array_random($colors),
                'icon' => cdn('img/app-list/square.png', false),
            ],
        ];
        $applicationsList = [
            [
                'title' => '课表查询',
                'route' => '/pages/timetable/timetable',
                'bg' => $icons['timetable']['bg'],
                'icon' => $icons['timetable']['icon'],
                'disabled' => false,
            ],
            [
                'title' => '成绩查询',
                'route' => '/pages/score/score',
                'bg' => $icons['score']['bg'],
                'icon' => $icons['score']['icon'],
            ],
            [
                'title' => '考试安排',
                'route' => '/pages/exam/exam',
                'bg' => $icons['exam']['bg'],
                'icon' => $icons['exam']['icon'],
            ],
            [
                'title' => '空教室',
                'route' => '/pages/freeroom/freeroom',
                'bg' => $icons['freeroom']['bg'],
                'icon' => $icons['freeroom']['icon'],
            ],
            [
                'title' => '教师查询',
                'route' => '/pages/teacher/teacher',
                'bg' => $icons['teacher']['bg'],
                'icon' => $icons['teacher']['icon'],
            ],
            [
                'title' => '一卡通',
                'route' => '/pages/card/card',
                'bg' => $icons['card']['bg'],
                'icon' => $icons['card']['icon'],
            ],
            [
                'title' => '借阅信息',
                'route' => '/pages/borrow/borrow',
                'bg' => $icons['borrow']['bg'],
                'icon' => $icons['borrow']['icon'],
            ],
            [
                'title' => '志愿者',
                'url' => url('/wechat/view') . '?url=' . urlencode('http://zhangyx.cn/VMS/WechatTimeResult.do?id={uno}'),
                'bg' => $icons['publicity']['bg'],
                // 'icon' => $icons['publicity']['icon'],
            ],
            [
                'title' => '吐个槽',
                'module' => 'feedback',
                'bg' => $icons['square']['bg'],
                'icon' => $icons['square']['icon'],
            ],
        ];

        $res = [
            'icons' => $icons,
            'app-list' => $applicationsList
        ];
        return RJM($res, 1, 'ok');
    }
}
