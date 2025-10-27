@extends('admin.layout.after_header')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/admin/attendance_detail.css') }}">
@endsection

@section('content')
    <div class="container">
        <h2 class="content-title">勤怠詳細</h2>

        @if(session('error'))
            <div class="alert alert-error">{{ session('error') }}</div>
        @endif

        <form action="{{ route('admin.attendance.update', $attendance->id) }}" method="POST">
            @csrf
            <table class="attendance-detail-table">
                <tr class="attendance-detail-table__row">
                    <th>名前</th>
                    <td>{{ $attendance->user->name }}</td>
                </tr>
                <tr class="attendance-detail-table__row">
                    <th>日付</th>
                    <td>
                        <span class="attendance-detail-table__year">{{ $attendance->date->year }}年</span>
                        <span class="attendance-detail-table__date">{{ $attendance->date->month }}月{{ $attendance->date->day }}日</span>
                    </td>
                </tr>
                <tr class="attendance-detail-table__row">
                    <th>出勤・退勤</th>
                    <td>
                        <input type="time" name="work_start" id="work_start" value="{{ old('work_start', $hasPendingRequest && $pendingRequest->work_start ? $pendingRequest->work_start->format('H:i') : ($attendance->work_start ? $attendance->work_start->format('H:i') : '')) }}" {{ $hasPendingRequest ? 'disabled' : '' }}>
                        @error('work_start')
                            <p class="error-message">{{ $message }}</p>
                        @enderror
                        ～
                        <input type="time" name="work_stop" id="work_stop" value="{{ old('work_stop', $hasPendingRequest && $pendingRequest->work_stop ? $pendingRequest->work_stop->format('H:i') : ($attendance->work_stop ? $attendance->work_stop->format('H:i') : '')) }}" {{ $hasPendingRequest ? 'disabled' : '' }}>
                        @error('work_stop')
                            <p class="error-message">{{ $message }}</p>
                        @enderror
                    </td>
                </tr>
                @if($hasPendingRequest && $pendingRequest->fixesBreakRequests->count() > 0)
                    @foreach ($pendingRequest->fixesBreakRequests as $index => $fixesBreak)
                    <tr class="attendance-detail-table__row">
                        <th>休憩{{ $index + 1 }}</th>
                        <td>
                            <input type="time" name="break_start[]" id="break_start_{{ $index }}" value="{{ old('break_start.' . $index, $fixesBreak->break_start ? $fixesBreak->break_start->format('H:i') : '') }}" disabled>
                            @error("break_start.{$index}")
                                <p class="error-message">{{ $message }}</p>
                            @enderror
                            ～
                            <input type="time" name="break_stop[]" id="break_stop_{{ $index }}" value="{{ old('break_stop.' . $index, $fixesBreak->break_stop ? $fixesBreak->break_stop->format('H:i') : '') }}" disabled>
                            @error("break_stop.{$index}")
                                <p class="error-message">{{ $message }}</p>
                            @enderror
                        </td>
                    </tr>
                    @endforeach
                @else
                    @foreach ($breakTimes as $index => $breakTime)
                    <tr class="attendance-detail-table__row">
                        <th>休憩{{ $index + 1 }}</th>
                        <td>
                            <input type="time" name="break_start[]" id="break_start_{{ $index }}" value="{{ old('break_start.' . $index, $breakTime->break_start ? $breakTime->break_start->format('H:i') : '') }}" {{ $hasPendingRequest ? 'disabled' : '' }}>
                            @error("break_start.{$index}")
                                <p class="error-message">{{ $message }}</p>
                            @enderror
                            ～
                            <input type="time" name="break_stop[]" id="break_stop_{{ $index }}" value="{{ old('break_stop.' . $index, $breakTime->break_stop ? $breakTime->break_stop->format('H:i') : '') }}" {{ $hasPendingRequest ? 'disabled' : '' }}>
                            @error("break_stop.{$index}")
                                <p class="error-message">{{ $message }}</p>
                            @enderror
                        </td>
                    </tr>
                    @endforeach
                @endif
                <tr class="attendance-detail-table__row">
                    <th>休憩{{ $breakTimes->count() + 1 }}</th>
                    <td>
                        <input type="time" name="break_start[]" id="break_start_new" value="{{ old('break_start.' . $breakTimes->count()) }}" {{ $hasPendingRequest ? 'disabled' : '' }}>
                        @error("break_start.{$breakTimes->count()}")
                            <p class="error-message">{{ $message }}</p>
                        @enderror
                        ～
                        <input type="time" name="break_stop[]" id="break_stop_new" value="{{ old('break_stop.' . $breakTimes->count()) }}" {{ $hasPendingRequest ? 'disabled' : '' }}>
                        @error("break_stop.{$breakTimes->count()}")
                            <p class="error-message">{{ $message }}</p>
                        @enderror
                    </td>
                </tr>
                <tr class="attendance-detail-table__row">
                    <th>備考</th>
                    <td>
                        <textarea name="remarks" id="remarks" cols="30" rows="5" class="attendance-detail__remarks" {{ $hasPendingRequest ? 'disabled' : '' }}>{{ old('remarks', $hasPendingRequest && $pendingRequest->request_reason ? $pendingRequest->request_reason : '') }}</textarea>
                        @error('remarks')
                            <p class="error-message">{{ $message }}</p>
                        @enderror
                    </td>
                </tr>
            </table>

            @if(!$hasPendingRequest)
            <div class="attendance-detail-table__button">
                <button type="submit" class="btn btn-primary">修正</button>
            </div>
            @else
            <div class="attendance-detail__waiting-approval">
                <p>*承認待ちのため修正はできません。</p>
            </div>
            @endif
        </form>
    </div>
@endsection