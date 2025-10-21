<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\FixesAttendanceRequest;
use App\Models\BreakTime;

class ApprovalController extends Controller
{
    // 管理者用修正申請一覧画面表示
    public function fixesRequestListShow()
    {
        // 全ユーザーの承認待ち修正申請を取得
        $pendingRequests = FixesAttendanceRequest::with(['attendance.user', 'fixesBreakRequests'])
            ->where('status', 'pending')
            ->orderBy('created_at', 'desc')
            ->get();
        
        // 全ユーザーの承認済み修正申請を取得
        $approvedRequests = FixesAttendanceRequest::with(['attendance.user', 'fixesBreakRequests'])
            ->where('status', 'approved')
            ->orderBy('created_at', 'desc')
            ->get();
        
        return view('admin.auth.fixes_request', compact('pendingRequests', 'approvedRequests'));
    }

    // 修正申請承認画面表示
    public function approvalPageShow($id)
    {
        $request = FixesAttendanceRequest::with(['attendance.user', 'fixesBreakRequests'])
            ->findOrFail($id);
        return view('admin.auth.approval', compact('request'));
    }

    // 修正申請承認処理
    public function approval($id)
    {
        $fixesRequest = FixesAttendanceRequest::with(['attendance.breakTimes', 'fixesBreakRequests'])
            ->findOrFail($id);
        
        // 既に承認済みの場合はリダイレクト
        if ($fixesRequest->status === 'approved') {
            return redirect()->route('admin.approval.page', $id)
                ->with('info', 'この申請は既に承認済みです。');
        }
        
        $attendance = $fixesRequest->attendance;
        
        // 出勤・退勤時刻を更新
        if ($fixesRequest->work_start) {
            $attendance->work_start = $fixesRequest->work_start;
        }
        if ($fixesRequest->work_stop) {
            $attendance->work_stop = $fixesRequest->work_stop;
        }
        $attendance->save();
        
        // 休憩時間を更新
        if ($fixesRequest->fixesBreakRequests->count() > 0) {
            // 既存の休憩時間を全て削除
            $attendance->breakTimes()->delete();
            
            // 新しい休憩時間を作成
            foreach ($fixesRequest->fixesBreakRequests as $fixesBreak) {
                BreakTime::create([
                    'attendance_id' => $attendance->id,
                    'break_start' => $fixesBreak->break_start,
                    'break_stop' => $fixesBreak->break_stop,
                ]);
            }
        }
        
        // 申請のステータスを承認済みに更新
        $fixesRequest->status = 'approved';
        $fixesRequest->save();
        
        return redirect()->route('admin.approval.page', $id)
            ->with('success');
    }
}
