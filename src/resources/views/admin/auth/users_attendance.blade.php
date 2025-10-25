@extends('admin.layout.after_header')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/admin/users_attendance.css') }}">
@endsection

@section('content')
    <div class="container">
        <h2 class="content-title">{{ $user->name }}さんの勤怠</h2>

        <!-- 日付ページネーション -->
        <div class="date-navigation">
            <div class="date-navigation__controls">
                <a href="{{ route('admin.users.attendance', ['id' => $user->id, 'year' => $prevMonth->year, 'month' => $prevMonth->month]) }}" class="nav-button nav-button--prev"></a>
                
                <div class="current-month">
                    <span class="calendar-icon">📅</span>
                    <span class="current-month__date">{{ $currentMonth->format('Y/m') }}</span>
                </div>
                
                <a href="{{ route('admin.users.attendance', ['id' => $user->id, 'year' => $nextMonth->year, 'month' => $nextMonth->month]) }}" class="nav-button nav-button--next"></a>
            </div>
        </div>

        <table class="users-attendance-table">
            <tr class="users-attendance-table__header">
                <th>日付</th>
                <th>出勤</th>
                <th>退勤</th>
                <th>休憩</th>
                <th>合計</th>
                <th>詳細</th>
            </tr>
            @forelse ($attendances as $attendance)
                @php
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
                @endphp
                <tr class="users-attendance-table__data">
                    <td>{{ $attendance->date->isoFormat('MM/DD(ddd)') }}</td>
                    <td>{{ $attendance->work_start ? $attendance->work_start->format('H:i') : '-' }}</td>
                    <td>{{ $attendance->work_stop ? $attendance->work_stop->format('H:i') : '-' }}</td>
                    <td>{{ sprintf('%02d:%02d', $breakHours, $breakMinutes) }}</td>
                    <td>{{ sprintf('%02d:%02d', $workHours, $workMinutes) }}</td>
                    <td><a href="{{ route('admin.attendance.detail', ['id' => $attendance->id]) }}">詳細</a></td>
                </tr>
            @empty
                <tr class="users-attendance-table__data">
                    <td colspan="6" style="text-align: center;">この月の勤怠データはありません</td>
                </tr>
            @endforelse
        </table>

        <!-- CSV出力ボタン -->
        <div class="csv-export-container">
            <a href="{{ route('admin.users.attendance.csv', ['id' => $user->id, 'year' => $currentMonth->year, 'month' => $currentMonth->month]) }}" class="csv-export-button">CSV出力</a>
        </div>
    </div>
@endsection