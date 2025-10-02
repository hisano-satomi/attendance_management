@extends('admin.layout.after_header')

@section('content')
    <div class="container">
        <h2 class="content-title">2023年6月1日の勤怠</h2>

        <table class="attendance-list__table">
            <tr class="attendance-list__header">
                <th>名前</th>
                <th>出勤</th>
                <th>退勤</th>
                <th>休憩</th>
                <th>合計</th>
                <th>詳細</th>
            </tr>
            <tr class="attendance-list__data">
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