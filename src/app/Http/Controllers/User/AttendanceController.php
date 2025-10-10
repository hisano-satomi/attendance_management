<?php

namespace App\Http\Controllers\user;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Attendance;
use App\Models\BreakTime;

class AttendanceController extends Controller
{
    // 勤怠登録画面表示
    public function attendancePageShow()
    {
        return view('user.auth.attendance');
    }

    // 勤怠登録機能-出勤処理
    public function workStart()
    {
        
    }

    // 勤怠登録機能-退勤処理
    public function workStop()
    {
        
    }

    // 勤怠登録機能-休憩入処理
    public function breakStart()
    {
        
    }

    // 勤怠登録機能-休憩戻処理
    public function breakStop()
    {
        
    }

    // 一般ユーザー用勤怠一覧画面表示
    public function attendanceListShow()
    {
        return view('user.auth.attendance_list');
    }

    // 一般ユーザー用勤怠詳細画面表示
    public function attendanceDetailShow()
    {
        return view('user.auth.attendance_detail');
    }
}
