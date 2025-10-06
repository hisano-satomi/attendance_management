@extends('user.layout.after_header')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/user/attendance.css') }}">
@endsection

@section('content')
    <div class="container">
        <div class="attendance-status">勤務外</div>
        <div class="attendance-date">2023/06/01(木)</div>
        <div class="attendance-time">08:00</div>
        <div class="attendance-action">
            <button class="attendance-action__work attendance-action__button--work-start">出勤</button>
            <button class="attendance-action__work attendance-action__button--work-end">退勤</button>
            <button class="attendance-action__break attendance-action__button--break-start">休憩入</button>
            <button class="attendance-action__break attendance-action__button--break-end">休憩戻</button>
            <p class="attendance-message">お疲れ様でした。</p>
        </div>
    </div>
@endsection
