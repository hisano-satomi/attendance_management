@extends('user.layout.after_header')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/user/attendance_list.css') }}">
@endsection


@section('content')
    <div class="container">
        <h2 class="content-title">勤怠一覧</h2>

        <!-- 日付ページネーション -->
        <div class="date-navigation">
            <div class="date-navigation__controls">
                <a href="{{ route('user.attendance.list', ['year' => $prevMonth->year, 'month' => $prevMonth->month]) }}" class="nav-button nav-button--prev">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <polyline points="15,18 9,12 15,6"></polyline>
                    </svg>
                </a>
                
                <div class="current-month">
                    <span class="calendar-icon">📅</span>
                    <span class="current-month__date">{{ $currentMonth->format('Y/m') }}</span>
                </div>
                
                <a href="{{ route('user.attendance.list', ['year' => $nextMonth->year, 'month' => $nextMonth->month]) }}" class="nav-button nav-button--next">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <polyline points="9,18 15,12 9,6"></polyline>
                    </svg>
                </a>
            </div>
        </div>

        <table class="attendance-list-table">
            <tr class="attendance-list-table__header">
                <th>日付</th>
                <th>出勤</th>
                <th>退勤</th>
                <th>休憩</th>
                <th>合計</th>
                <th>詳細</th>
            </tr>
            @foreach ($attendances as $attendance)
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
                <tr class="attendance-list-table__data">
                    <td>{{ $attendance->date->isoFormat('MM/DD(ddd)') }}</td>
                    <td>{{ $attendance->work_start ? $attendance->work_start->format('H:i') : '-' }}</td>
                    <td>{{ $attendance->work_stop ? $attendance->work_stop->format('H:i') : '-' }}</td>
                    <td>{{ sprintf('%02d:%02d', $breakHours, $breakMinutes) }}</td>
                    <td>{{ sprintf('%02d:%02d', $workHours, $workMinutes) }}</td>
                    <td><a href="{{ route('user.attendance.detail', $attendance->id) }}">詳細</a></td>
                </tr>
            @endforeach
        </table>
    </div>
@endsection