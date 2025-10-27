<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\AttendanceRequest;
use App\Models\Attendance;
use App\Models\BreakTime;
use Carbon\Carbon;

class FixesRequestController extends Controller
{
    // 管理者による勤怠修正処理（直接データベースを更新）
    public function fixesRequest(AttendanceRequest $request, $id)
    {
        // 勤怠記録を取得
        $attendance = Attendance::findOrFail($id);
        
        // 時刻を日時形式に変換（勤怠記録の日付と組み合わせる）
        if ($request->work_start) {
            $attendance->work_start = Carbon::parse($attendance->date->format('Y-m-d') . ' ' . $request->work_start);
        }
        
        if ($request->work_stop) {
            $attendance->work_stop = Carbon::parse($attendance->date->format('Y-m-d') . ' ' . $request->work_stop);
        }
        
        // 勤怠記録を更新
        $attendance->save();
        
        // 既存の休憩時間を全て削除
        $attendance->breakTimes()->delete();
        
        // 新しい休憩時間を作成
        if ($request->has('break_start') && is_array($request->break_start)) {
            foreach ($request->break_start as $index => $breakStart) {
                if ($breakStart && isset($request->break_stop[$index]) && $request->break_stop[$index]) {
                    $breakStartTime = Carbon::parse($attendance->date->format('Y-m-d') . ' ' . $breakStart);
                    $breakStopTime = Carbon::parse($attendance->date->format('Y-m-d') . ' ' . $request->break_stop[$index]);
                    
                    BreakTime::create([
                        'attendance_id' => $attendance->id,
                        'break_start' => $breakStartTime,
                        'break_stop' => $breakStopTime,
                    ]);
                }
            }
        }
        
        return redirect()->route('admin.attendance.detail', $id)
            ->with('success');
    }
}
