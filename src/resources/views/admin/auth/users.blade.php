@extends('admin.layout.after_header')

@section('content')
    <h2 class="content-title">スタッフ一覧</h2>    

    <table class="users-table">
        <tr class="users-table__header">
            <th>名前</th>
            <th>メールアドレス</th>
            <th>月次勤怠</th>
        </tr>
        <tr class="users-table__data">
            <td>山田 太郎</td>
            <td>yamada@example.com</td>
            <td>詳細</td>
        </tr>
    </table>
@endsection