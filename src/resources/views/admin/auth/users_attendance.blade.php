@extends('admin.layout.after_header')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/admin/users_attendance.css') }}">
@endsection

@section('content')
    <div class="container">
        <h2 class="content-title">山田太郎さんの勤怠</h2>

        <!-- 日付ページネーション -->
        <div class="date-navigation">
            <div class="date-navigation__controls">
                <button class="nav-button nav-button--prev" type="button">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <polyline points="15,18 9,12 15,6"></polyline>
                    </svg>
                </button>
                
                <div class="current-month">
                    <span class="calendar-icon">📅</span>
                    <span class="current-month__date">2023/06</span>
                </div>
                
                <button class="nav-button nav-button--next" type="button">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <polyline points="9,18 15,12 9,6"></polyline>
                    </svg>
                </button>
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
            <tr class="users-attendance-table__data">
                <td>06/01(木)</td>
                <td>09:00</td>
                <td>18:00</td>
                <td>01:00</td>
                <td>08:00</td>
                <td><a href="#">詳細</a></td>
            </tr>
        </table>
    </div>
@endsection