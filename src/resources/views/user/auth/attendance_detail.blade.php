@extends('user.layout.after_header')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/user/attendance_detail.css') }}">
@endsection

@section('content')
    <div class="container">
        <h2 class="content-title">勤怠詳細</h2>

        <form action="">
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
                        <input type="time" name="work_start" id="work_start" value="{{ $attendance->work_start ? $attendance->work_start->format('H:i') : '' }}">
                        ～
                        <input type="time" name="work_stop" id="work_stop" value="{{ $attendance->work_stop ? $attendance->work_stop->format('H:i') : '' }}">
                    </td>
                    <!-- 承認待ちだったら入力できないようにする(申請している時間を表示) -->
                </tr>
                @foreach ($breakTimes as $index => $breakTime)
                <tr class="attendance-detail-table__row">
                    <th>休憩{{ $index + 1 }}</th>
                    <td>
                        <input type="time" name="break_start[]" id="break_start_{{ $index }}" value="{{ $breakTime->break_start ? $breakTime->break_start->format('H:i') : '' }}">
                        ～
                        <input type="time" name="break_stop[]" id="break_stop_{{ $index }}" value="{{ $breakTime->break_stop ? $breakTime->break_stop->format('H:i') : '' }}">
                        <!-- 承認待ちだったら入力できないようにする(申請している時間を表示) -->
                    </td>
                </tr>
                @endforeach
                <tr class="attendance-detail-table__row">
                    <!-- 休憩1がなければ休憩1と表示、承認待ちだったら入力欄を非表示 -->
                    <th>休憩2</th>
                    <td>
                        <input type="time" name="start_time" id="start_time">
                        ～
                        <input type="time" name="end_time" id="end_time">
                    </td>
                </tr>
                <tr class="attendance-detail-table__row">
                    <th>備考</th>
                    <td>
                        <textarea name="remarks" id="remarks" cols="30" rows="5" class="attendance-detail__remarks"></textarea>
                        <!-- 承認待ちだったら入力できないようにする(申請している内容を表示) -->
                    </td>
                </tr>
            </table>

            <div class="attendance-detail-table__button">
                <button type="submit" class="btn btn-primary">修正</button>
            </div>
            <!-- 承認待ちだったら以下を表示 -->
            <div class="attendance-detail__waiting-approval">
                <p>*承認待ちのため修正はできません。</p>
            </div>
        </form>
    </div>
@endsection