@extends('user.layout.after_header')

@section('content')
    <div class="container">
        <h2 class="content-title">勤怠一覧</h2>

        <!-- ページネーションを作成する -->

        <table class="attendance-list-table">
            <tr class="attendance-list-table__header">
                <th>日付</th>
                <th>出勤</th>
                <th>退勤</th>
                <th>休憩</th>
                <th>合計</th>
                <th>詳細</th>
            </tr>
            <tr class="attendance-list-table__data">
                <td>06/01(木)</td>
                <td>09:00</td>
                <td>18:00</td>
                <td>1:00</td>
                <td>8:00</td>
                <td><a href="#">詳細</a></td>
            </tr>
        </table>
    </div>
@endsection