<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Attendance;
use App\Models\BreakTime;
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
    public function attendanceDetailShow()
    {
        return view('admin.auth.attendance_detail');
    }
}
