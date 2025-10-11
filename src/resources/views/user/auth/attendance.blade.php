@extends('user.layout.after_header')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/user/attendance.css') }}">
@endsection

@section('content')
    <div class="container">
        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        
        @if(session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif
        
        <div class="attendance-status">
            @if($attendance)
                @switch($attendance->status)
                    @case('working')
                        勤務中
                        @break
                    @case('breaking')
                        休憩中
                        @break
                    @case('done')
                        勤務終了
                        @break
                    @default
                        勤務外
                @endswitch
            @else
                勤務外
            @endif
        </div>
        
        <div class="attendance-date">{{ Carbon\Carbon::now()->format('Y/m/d(D)') }}</div>
        <div class="attendance-time">{{ Carbon\Carbon::now()->format('H:i') }}</div>
        
        <div class="attendance-action">
            @if(!$attendance)
                {{-- 出勤前：出勤ボタンのみ表示 --}}
                <form action="{{ route('user.attendance.work_start') }}" method="POST" style="display: inline;">
                    @csrf
                    <button type="submit" class="attendance-action__work attendance-action__button--work-start">出勤</button>
                </form>
            @elseif($attendance->status === 'working')
                {{-- 勤務中：退勤ボタンと休憩入ボタンを表示 --}}
                <button class="attendance-action__work attendance-action__button--work-end">退勤</button>
                <button class="attendance-action__break attendance-action__button--break-start">休憩入</button>
            @elseif($attendance->status === 'breaking')
                {{-- 休憩中：休憩戻ボタンのみ表示 --}}
                <button class="attendance-action__break attendance-action__button--break-end">休憩戻</button>
            @else
                {{-- 勤務終了 --}}
                <p class="attendance-message">お疲れ様でした。</p>
            @endif
        </div>
    </div>
@endsection
