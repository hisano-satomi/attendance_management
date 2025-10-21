<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\AttendanceRequest;
use App\Models\FixesAttendanceRequest;
use App\Models\FixesBreakRequest;
use App\Models\Attendance;
use App\Models\BreakTime;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class FixesRequestController extends Controller
{
    // 修正申請処理
    public function fixesRequest(AttendanceRequest $request)
    {
        $userId = Auth::id();
        
        // 勤怠記録を取得し、ログインユーザーのものか確認
        $attendance = Attendance::where('id', $request->attendance_id)
            ->where('user_id', $userId)
            ->firstOrFail();
        
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
                if ($breakStart && isset($request->break_stop[$index])) {
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
        
        return redirect()->route('user.attendance.detail', ['id' => $attendance->id])
            ->with('success');
    }
    
    // 修正申請一覧画面表示
    public function fixesRequestListShow()
    {
        $userId = Auth::id();
        
        // ログインユーザーの修正申請を取得（勤怠記録とユーザー情報も含める）
        $pendingRequests = FixesAttendanceRequest::with(['attendance.user', 'fixesBreakRequests'])
            ->whereHas('attendance', function ($query) use ($userId) {
                $query->where('user_id', $userId);
            })
            ->where('status', 'pending')
            ->orderBy('created_at', 'desc')
            ->get();
        
        $approvedRequests = FixesAttendanceRequest::with(['attendance.user', 'fixesBreakRequests'])
            ->whereHas('attendance', function ($query) use ($userId) {
                $query->where('user_id', $userId);
            })
            ->where('status', 'approved')
            ->orderBy('created_at', 'desc')
            ->get();
        
        return view('user.auth.fixes_request', compact('pendingRequests', 'approvedRequests'));
    }
}
