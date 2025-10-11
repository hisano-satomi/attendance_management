<?php

namespace App\Http\Controllers\user;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Models\Attendance;
use App\Models\BreakTime;

class AttendanceController extends Controller
{
    // 勤怠登録画面表示
    public function attendancePageShow()
    {
        $user = Auth::user();
        $attendance = Attendance::where('user_id', $user->id)->where('date', Carbon::today())->first();
        return view('user.auth.attendance', compact('attendance'));
    }

    // 勤怠登録機能-出勤処理
    public function workStart()
    {
        $userId = Auth::id();
        $today = Carbon::today();
        
        // 今日の勤怠記録が既に存在するかチェック
        $existingAttendance = Attendance::where('user_id', $userId)
            ->where('date', $today)
            ->first();
        
        if ($existingAttendance) {
            return redirect()->route('user.attendance')
                ->with('error', '本日は既に出勤登録されています。');
        }
        
        // 新しい勤怠記録を作成
        Attendance::create([
            'user_id' => $userId,
            'work_start' => Carbon::now(),
            'date' => $today,
            'status' => 'working'
        ]);
        
        return redirect()->route('user.attendance')
            ->with('success', '出勤登録が完了しました。');
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
