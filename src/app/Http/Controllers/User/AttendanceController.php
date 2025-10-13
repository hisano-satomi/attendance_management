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
            ->with('success',);
    }

    // 勤怠登録機能-退勤処理
    public function workStop()
    {
        $userId = Auth::id();
        $today = Carbon::today();
        
        // 今日の出勤記録が存在するかチェック
        $existingAttendance = Attendance::where('user_id', $userId)
            ->where('date', $today)
            ->first();
        
        if (!$existingAttendance) {
            return redirect()->route('user.attendance')
                ->with('error', '本日は出勤登録されていません。');
        }

        // 出勤中であれば退勤処理を実行可能（勤怠更新）
        if ($existingAttendance->status === 'working') {
            $existingAttendance->update([
                'work_stop' => Carbon::now(),
                'status' => 'done'
            ]);
        }

        return redirect()->route('user.attendance')
            ->with('success',);
    }

    // 勤怠登録機能-休憩入処理
    public function breakStart()
    {
        $userId = Auth::id();
        $today = Carbon::today();
        
        // 今日の出勤記録が存在するかチェック
        $existingAttendance = Attendance::where('user_id', $userId)
            ->where('date', $today)
            ->first();

        if (!$existingAttendance) {
            return redirect()->route('user.attendance')
                ->with('error', '本日は出勤登録されていません。');
        }

        // 出勤中であれば休憩入処理を実行可能
        if ($existingAttendance->status === 'working') {
            // 勤怠ステータスを更新
            $existingAttendance->update([
                'status' => 'breaking'
            ]);

            // 休憩時間レコードを作成
            BreakTime::create([
                'attendance_id' => $existingAttendance->id,
                'break_start' => Carbon::now(),
            ]);
        }

        return redirect()->route('user.attendance')
            ->with('success',);
    }

    // 勤怠登録機能-休憩戻処理
    public function breakStop()
    {
        $userId = Auth::id();
        $today = Carbon::today();
        
        // 今日の出勤記録が存在するかチェック
        $existingAttendance = Attendance::where('user_id', $userId)
            ->where('date', $today)
            ->first();

        if (!$existingAttendance) {
            return redirect()->route('user.attendance')
                ->with('error', '本日は出勤登録されていません。');
        }

        // 出勤中であれば休憩戻処理を実行可能
        if ($existingAttendance->status === 'breaking') {
            // 勤怠ステータスを更新
            $existingAttendance->update([
                'status' => 'working'
            ]);

            // 休憩時間レコードを更新
            BreakTime::where('attendance_id', $existingAttendance->id)->update([
                'break_stop' => Carbon::now(),
            ]);

            return redirect()->route('user.attendance')
            ->with('success',);
        }
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
