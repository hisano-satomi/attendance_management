<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\AttendanceRequest;
use App\Models\Attendance;
use App\Models\FixesAttendanceRequest;
use App\Models\FixesBreakRequest;
use Carbon\Carbon;

class FixesRequestController extends Controller
{
    // 管理者による勤怠修正申請処理
    public function fixesRequest(AttendanceRequest $request, $id)
    {
        // 勤怠記録を取得
        $attendance = Attendance::findOrFail($id);
        
        // 既に承認待ちの申請がないかチェック
        $existingRequest = FixesAttendanceRequest::where('attendance_id', $id)
            ->where('status', 'pending')
            ->first();
        
        if ($existingRequest) {
            return redirect()->route('admin.attendance.detail', $id)
                ->with('error', '既に承認待ちの修正申請が存在します。');
        }
        
        // 時刻を日時形式に変換（勤怠記録の日付と組み合わせる）
        $workStart = null;
        $workStop = null;
        
        if ($request->work_start) {
            $workStart = Carbon::parse($attendance->date->format('Y-m-d') . ' ' . $request->work_start);
        }
        
        if ($request->work_stop) {
            $workStop = Carbon::parse($attendance->date->format('Y-m-d') . ' ' . $request->work_stop);
        }
        
        // 修正申請を作成
        $fixesAttendanceRequest = FixesAttendanceRequest::create([
            'attendance_id' => $attendance->id,
            'work_start' => $workStart,
            'work_stop' => $workStop,
            'request_reason' => $request->remarks,
            'status' => 'pending',
        ]);
        
        // 休憩時間の修正申請を作成（配列で送信される場合）
        if ($request->has('break_start') && is_array($request->break_start)) {
            foreach ($request->break_start as $index => $breakStart) {
                if ($breakStart && isset($request->break_stop[$index]) && $request->break_stop[$index]) {
                    $breakStartTime = Carbon::parse($attendance->date->format('Y-m-d') . ' ' . $breakStart);
                    $breakStopTime = Carbon::parse($attendance->date->format('Y-m-d') . ' ' . $request->break_stop[$index]);
                    
                    FixesBreakRequest::create([
                        'fixes_attendance_request_id' => $fixesAttendanceRequest->id,
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
