@extends('admin.layout.after_header')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/admin/attendance_list.css') }}">
@endsection

@section('content')
    <div class="container">
        <h2 class="content-title">2023年6月1日の勤怠</h2>

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
                    <span class="current-month__date">2023/06/01</span>
                </div>
                
                <button class="nav-button nav-button--next" type="button">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <polyline points="9,18 15,12 9,6"></polyline>
                    </svg>
                </button>
            </div>
        </div>

        <table class="attendance-list-table">
            <tr class="attendance-list-table__header">
                <th>名前</th>
                <th>出勤</th>
                <th>退勤</th>
                <th>休憩</th>
                <th>合計</th>
                <th>詳細</th>
            </tr>
            <tr class="attendance-list-table__data">
                <td>山田 太郎</td>
                <td>09:00</td>
                <td>18:00</td>
                <td>1:00</td>
                <td>8:00</td>
                <td><a href="#">詳細</a></td>
            </tr>
        </table>
    </div>
@endsection