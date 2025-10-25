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

    // スタッフ別勤怠一覧のCSV出力
    public function exportCsv(Request $request, $id)
    {
        $user = User::findOrFail($id);
        
        // リクエストから年月を取得（デフォルトは今月）
        $year = $request->input('year', Carbon::now()->year);
        $month = $request->input('month', Carbon::now()->month);
        
        // 指定された年月の最初と最後の日を取得
        $startDate = Carbon::create($year, $month, 1)->startOfDay();
        $endDate = Carbon::create($year, $month, 1)->endOfMonth()->endOfDay();
        
        // 指定されたユーザーの指定月の勤怠記録を取得
        $attendances = Attendance::where('user_id', $id)
            ->whereBetween('date', [$startDate, $endDate])
            ->with('breakTimes')
            ->orderBy('date', 'asc')
            ->get();
        
        // CSVファイル名を生成
        $filename = sprintf('%s_%s年%s月_勤怠.csv', $user->name, $year, $month);
        
        // レスポンスヘッダーを設定
        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];
        
        // CSVデータを生成するコールバック
        $callback = function() use ($attendances) {
            $file = fopen('php://output', 'w');
            
            // BOMを追加（Excelで文字化けしないようにするため）
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));
            
            // ヘッダー行を追加
            fputcsv($file, ['日付', '出勤', '退勤', '休憩', '合計']);
            
            // データ行を追加
            foreach ($attendances as $attendance) {
                // 休憩時間の合計を計算（秒単位）
                $totalBreakSeconds = 0;
                foreach ($attendance->breakTimes as $breakTime) {
                    if ($breakTime->break_start && $breakTime->break_stop) {
                        $totalBreakSeconds += $breakTime->break_stop->diffInSeconds($breakTime->break_start);
                    }
                }
                
                // 勤務時間の合計を計算（秒単位）
                $totalWorkSeconds = 0;
                if ($attendance->work_start && $attendance->work_stop) {
                    $totalWorkSeconds = $attendance->work_stop->diffInSeconds($attendance->work_start) - $totalBreakSeconds;
                }
                
                // 時間と分に変換
                $breakHours = floor($totalBreakSeconds / 3600);
                $breakMinutes = floor(($totalBreakSeconds % 3600) / 60);
                $workHours = floor($totalWorkSeconds / 3600);
                $workMinutes = floor(($totalWorkSeconds % 3600) / 60);
                
                fputcsv($file, [
                    $attendance->date->isoFormat('MM/DD(ddd)'),
                    $attendance->work_start ? $attendance->work_start->format('H:i') : '-',
                    $attendance->work_stop ? $attendance->work_stop->format('H:i') : '-',
                    sprintf('%02d:%02d', $breakHours, $breakMinutes),
                    sprintf('%02d:%02d', $workHours, $workMinutes),
                ]);
            }
            
            fclose($file);
        };
        
        return response()->stream($callback, 200, $headers);
    }
}
