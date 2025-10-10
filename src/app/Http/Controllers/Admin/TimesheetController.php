<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Attendance;
use App\Models\BreakTime;

class TimesheetController extends Controller
{
    // 勤怠一覧画面表示
    public function attendanceListShow()
    {
        return view('admin.auth.attendance_list');
    }

    // 勤怠詳細画面表示
    public function attendanceDetailShow()
    {
        return view('admin.auth.attendance_detail');
    }
}
