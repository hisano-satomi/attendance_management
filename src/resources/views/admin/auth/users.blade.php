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
            @foreach ($users as $user)
            <tr class="users-table__data">
                <td>{{ $user->name }}</td>
                <td>{{ $user->email }}</td>
                <td><a href="{{ route('admin.users.attendance', ['id' => $user->id]) }}">詳細</a></td>
            </tr>
            @endforeach
        </table>
    </div>
@endsection