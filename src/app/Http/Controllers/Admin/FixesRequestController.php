<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\FixesAttendanceRequest;
use App\Models\FixesBreakRequest;

class FixesRequestController extends Controller
{
    // 申請一覧画面表示
    public function fixesRequestListShow()
    {
        return view('admin.auth.fixes_request');
    }

    // 管理者による勤怠データ修正処理
    public function fixesRequest(Request $request, $id)
    {
        // バリデーション
        $request->validate([
            'work_start' => 'required|date_format:H:i',
            'work_stop' => 'required|date_format:H:i',
            'break_start' => 'array',
            'break_start.*' => 'nullable|date_format:H:i',
            'break_stop' => 'array',
            'break_stop.*' => 'nullable|date_format:H:i',
        ]);

        // 勤怠記録を取得
        $attendance = Attendance::findOrFail($id);
        
        // 出勤・退勤時刻を更新
        $attendance->work_start = Carbon::parse($attendance->date->format('Y-m-d') . ' ' . $request->work_start);
        $attendance->work_stop = Carbon::parse($attendance->date->format('Y-m-d') . ' ' . $request->work_stop);
        $attendance->save();

        // 既存の休憩時間を更新
        if ($request->has('break_start') && $request->has('break_stop')) {
            $breakStarts = $request->break_start;
            $breakStops = $request->break_stop;
            
            // 既存の休憩記録を取得
            $existingBreaks = $attendance->breakTimes;
            
            foreach ($breakStarts as $index => $breakStart) {
                if (!empty($breakStart) && !empty($breakStops[$index])) {
                    if (isset($existingBreaks[$index])) {
                        // 既存の休憩記録を更新
                        $existingBreaks[$index]->update([
                            'break_start' => Carbon::parse($attendance->date->format('Y-m-d') . ' ' . $breakStart),
                            'break_stop' => Carbon::parse($attendance->date->format('Y-m-d') . ' ' . $breakStops[$index]),
                        ]);
                    } else {
                        // 新しい休憩記録を作成
                        BreakTime::create([
                            'attendance_id' => $attendance->id,
                            'break_start' => Carbon::parse($attendance->date->format('Y-m-d') . ' ' . $breakStart),
                            'break_stop' => Carbon::parse($attendance->date->format('Y-m-d') . ' ' . $breakStops[$index]),
                        ]);
                    }
                }
            }
        }

        return redirect()->route('admin.attendance.detail', $id)
            ->with('success', '勤怠データを更新しました。');
    }
}
