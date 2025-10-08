@extends('admin.layout.after_header')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/admin/users.css') }}">
@endsection

@section('content')
    <div class="container">
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
                <td><a href="#">詳細</a></td>
            </tr>
        </table>
    </div>
@endsection