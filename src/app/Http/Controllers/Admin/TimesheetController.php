<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Attendance;
use App\Models\BreakTime;
use App\Models\FixesAttendanceRequest;
use Carbon\Carbon;

class TimesheetController extends Controller
{
    // 勤怠一覧画面表示
    public function attendanceListShow(Request $request)
    {
        // リクエストから日付を取得（デフォルトは今日）
        $dateString = $request->input('date', Carbon::today()->toDateString());
        $currentDate = Carbon::parse($dateString);
        
        // 前日と次の日の情報を計算
        $prevDate = $currentDate->copy()->subDay();
        $nextDate = $currentDate->copy()->addDay();
        
        // 指定された日付の全ユーザーの勤怠記録を取得
        $attendances = Attendance::whereDate('date', $currentDate)
            ->with(['user', 'breakTimes'])
            ->orderBy('user_id', 'asc')
            ->get();
        
        return view('admin.auth.attendance_list', compact('attendances', 'currentDate', 'prevDate', 'nextDate'));
    }

    // 勤怠詳細画面表示
    public function attendanceDetailShow($id)
    {
        // IDで勤怠記録を取得（管理者は全ユーザーのデータにアクセス可能）
        $attendance = Attendance::where('id', $id)
            ->with(['user', 'breakTimes'])
            ->firstOrFail();
        
        $breakTimes = $attendance->breakTimes;
        
        // 修正申請中かどうかを確認し、申請データを取得
        $pendingRequest = FixesAttendanceRequest::where('attendance_id', $id)
            ->where('status', 'pending')
            ->with('fixesBreakRequests')
            ->first();
        
        $hasPendingRequest = $pendingRequest !== null;
        
        return view('admin.auth.attendance_detail', compact('attendance', 'breakTimes', 'hasPendingRequest', 'pendingRequest'));
    }
}
