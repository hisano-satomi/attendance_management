@extends('admin.layout.after_header')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/admin/attendance_detail.css') }}">
@endsection

@section('content')
    <div class="container">
        <h2 class="content-title">勤怠詳細</h2>

        <form action="{{ route('admin.attendance.update', $attendance->id) }}" method="POST">
            @csrf
            <table class="attendance-detail-table">
                <tr class="attendance-detail-table__row">
                    <th>名前</th>
                    <td>{{ $attendance->user->name }}</td>
                </tr>
                <tr class="attendance-detail-table__row">
                    <th>日付</th>
                    <td class="attendance-detail-table__year">{{ $attendance->date->year }}年</td>
                    <td class="attendance-detail-table__date">{{ $attendance->date->month }}月{{ $attendance->date->day }}日</td>
                </tr>
                <tr class="attendance-detail-table__row">
                    <th>出勤・退勤</th>
                    <td>
                        <input type="time" name="work_start" id="work_start" value="{{ $hasPendingRequest && $pendingRequest->work_start ? $pendingRequest->work_start->format('H:i') : ($attendance->work_start ? $attendance->work_start->format('H:i') : '') }}" {{ $hasPendingRequest ? 'disabled' : '' }}>
                        ～
                        <input type="time" name="work_stop" id="work_stop" value="{{ $hasPendingRequest && $pendingRequest->work_stop ? $pendingRequest->work_stop->format('H:i') : ($attendance->work_stop ? $attendance->work_stop->format('H:i') : '') }}" {{ $hasPendingRequest ? 'disabled' : '' }}>
                    </td>
                </tr>
                @if($hasPendingRequest && $pendingRequest->fixesBreakRequests->count() > 0)
                    @foreach ($pendingRequest->fixesBreakRequests as $index => $fixesBreak)
                    <tr class="attendance-detail-table__row">
                        <th>休憩{{ $index + 1 }}</th>
                        <td>
                            <input type="time" name="break_start[]" id="break_start_{{ $index }}" value="{{ $fixesBreak->break_start ? $fixesBreak->break_start->format('H:i') : '' }}" disabled>
                            ～
                            <input type="time" name="break_stop[]" id="break_stop_{{ $index }}" value="{{ $fixesBreak->break_stop ? $fixesBreak->break_stop->format('H:i') : '' }}" disabled>
                        </td>
                    </tr>
                    @endforeach
                @else
                    @foreach ($breakTimes as $index => $breakTime)
                    <tr class="attendance-detail-table__row">
                        <th>休憩{{ $index + 1 }}</th>
                        <td>
                            <input type="time" name="break_start[]" id="break_start_{{ $index }}" value="{{ $breakTime->break_start ? $breakTime->break_start->format('H:i') : '' }}" {{ $hasPendingRequest ? 'disabled' : '' }}>
                            ～
                            <input type="time" name="break_stop[]" id="break_stop_{{ $index }}" value="{{ $breakTime->break_stop ? $breakTime->break_stop->format('H:i') : '' }}" {{ $hasPendingRequest ? 'disabled' : '' }}>
                        </td>
                    </tr>
                    @endforeach
                @endif
                <tr class="attendance-detail-table__row">
                    <th>休憩{{ $breakTimes->count() + 1 }}</th>
                    <td>
                        <input type="time" name="break_start[]" id="break_start_new" {{ $hasPendingRequest ? 'disabled' : '' }}>
                        ～
                        <input type="time" name="break_stop[]" id="break_stop_new" {{ $hasPendingRequest ? 'disabled' : '' }}>
                    </td>
                </tr>
                <tr class="attendance-detail-table__row">
                    <th>備考</th>
                    <td>
                        <textarea name="remarks" id="remarks" cols="30" rows="5" class="attendance-detail__remarks" {{ $hasPendingRequest ? 'disabled' : '' }} readonly>{{ $hasPendingRequest && $pendingRequest->request_reason ? $pendingRequest->request_reason : '管理者による直接修正' }}</textarea>
                    </td>
                </tr>
            </table>

            @if(!$hasPendingRequest)
            <div class="attendance-detail-table__button">
                <button type="submit" class="btn btn-primary">修正</button>
            </div>
            @else
            <div class="attendance-detail__waiting-approval">
                <p>*承認待ちの修正申請があるため、現在は修正できません。</p>
            </div>
            @endif
        </form>
    </div>
@endsection