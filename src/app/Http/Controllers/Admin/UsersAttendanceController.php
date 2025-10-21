<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Attendance;
use Carbon\Carbon;

class UsersAttendanceController extends Controller
{
    // スタッフ一覧画面表示
    public function usersListShow()
    {
        $users = User::all();
        return view('admin.auth.users', compact('users'));
    }

    // スタッフ別勤怠一覧画面表示
    public function usersAttendanceShow(Request $request, $id)
    {
        $user = User::findOrFail($id);
        
        // リクエストから年月を取得（デフォルトは今月）
        $year = $request->input('year', Carbon::now()->year);
        $month = $request->input('month', Carbon::now()->month);
        
        // 指定された年月の最初と最後の日を取得
        $startDate = Carbon::create($year, $month, 1)->startOfDay();
        $endDate = Carbon::create($year, $month, 1)->endOfMonth()->endOfDay();
        
        // 前月と次月の情報を計算
        $prevMonth = Carbon::create($year, $month, 1)->subMonth();
        $nextMonth = Carbon::create($year, $month, 1)->addMonth();
        $currentMonth = Carbon::create($year, $month, 1);
        
        // 指定されたユーザーの指定月の勤怠記録を取得
        $attendances = Attendance::where('user_id', $id)
            ->whereBetween('date', [$startDate, $endDate])
            ->with('breakTimes')
            ->orderBy('date', 'desc')
            ->get();
        
        return view('admin.auth.users_attendance', compact('user', 'attendances', 'currentMonth', 'prevMonth', 'nextMonth'));
    }
}
