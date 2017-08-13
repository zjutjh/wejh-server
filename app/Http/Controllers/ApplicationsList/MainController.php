<?php

namespace App\Http\Controllers\ApplicationsList;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class MainController extends Controller
{
    public function applicationsList(Request $request) {
        $applicationsList = [
            [
                'title' => '课表查询',
                'route' => '/pages/timetable/timetable',
                'bg' => 'blue',
                'icon' => url('img/app-list/timetable.png'),
                'disabled' => false,
            ],
            [
                'title' => '成绩查询',
                'route' => '/pages/score/score',
                'bg' => 'red',
                'icon' => url('img/app-list/score.png'),
            ],
            [
                'title' => '考试安排',
                'route' => '/pages/exam/exam',
                'bg' => 'green',
                'icon' => url('img/app-list/exam.png'),
            ],
            [
                'title' => '空教室',
                'route' => '/pages/freeroom/freeroom',
                'bg' => 'red',
                'icon' => url('img/app-list/freeroom.png'),
            ],
            [
                'title' => '学生查询',
                'route' => '/pages/student/student',
                'bg' => 'purple',
                'icon' => url('img/app-list/student.png'),
            ],
            [
                'title' => '一卡通',
                'route' => '/pages/card/card',
                'bg' => 'yellow',
                'icon' => url('img/app-list/card.png'),
            ],
            [
                'title' => '借阅信息',
                'route' => '/pages/borrow/borrow',
                'bg' => 'blue',
                'icon' => url('img/app-list/borrow.png'),
            ],
        ];

        $res = [
            'app-list' => $applicationsList
        ];
        return RJM($res, 1, 'ok');
    }
}
