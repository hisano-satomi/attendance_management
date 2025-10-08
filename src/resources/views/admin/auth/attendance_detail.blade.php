@extends('admin.layout.after_header')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/admin/attendance_detail.css') }}">
@endsection

@section('content')
    <div class="container">
        <h2 class="content-title">勤怠詳細</h2>

        <form action="">
            <table class="attendance-detail-table">
                <tr class="attendance-detail-table__row">
                    <th>名前</th>
                    <td>山田 太郎</td>
                </tr>
                <tr class="attendance-detail-table__row">
                    <th>日付</th>
                    <td class="attendance-detail-table__year">2023年</td>
                    <td class="attendance-detail-table__date">6月1日</td>
                </tr>
                <tr class="attendance-detail-table__row">
                    <th>出勤・退勤</th>
                    <td>
                        <input type="time" name="start_time" id="start_time">
                        ～
                        <input type="time" name="end_time" id="end_time">
                    </td>
                </tr>
                <tr class="attendance-detail-table__row">
                    <th>休憩</th>
                    <td>
                        <input type="time" name="start_time" id="start_time">
                        ～
                        <input type="time" name="end_time" id="end_time">
                    </td>
                </tr>
                <tr class="attendance-detail-table__row">
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
                    </td>
                </tr>
            </table>

            <div class="attendance-detail-table__button">
                <button type="submit" class="btn btn-primary">修正</button>
            </div>
        </form>
    </div>
@endsection