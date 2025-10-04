@extends('admin.layout.after_header')

@section('content')
    <div class="container">
        <h2 class="content-title">山田太郎さんの勤怠</h2>

        <!-- ページネーションを作成する -->

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
                <td>06/01（火）</td>
                <td>09:00</td>
                <td>18:00</td>
                <td>01:00</td>
                <td>08:00</td>
                <td><a href="#">詳細を見る</a></td>
            </tr>
        </table>
    </div>
@endsection