<?php

namespace App\Http\Controllers\user;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AttendanceController extends Controller
{
    // 一般ユーザー用勤怠登録画面表示
    public function attendancePageShow()
    {
        return view('user.auth.attendance');
    }

    // 一般ユーザー用勤怠一覧画面表示
    public function attendanceListShow()
    {
        return view('user.auth.attendance_list');
    }
}
