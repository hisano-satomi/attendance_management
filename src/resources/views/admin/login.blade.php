@extends('admin.layout.before_header')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/admin/login.css') }}">
@endsection

@section('content')
    <div class="content">
        <h2 class="content-title">管理者ログイン</h2>
        <form class="content-form" action="{{ route('admin.login') }}" method="POST">
        @csrf
            <div class="content-form--item">
                <label for="email">メールアドレス</label>
                <input type="email" id="email" name="email">
            </div>
            <div class="content-form--item">
                <label for="password">パスワード</label>
                <input type="password" id="password" name="password">
            </div>
            <button class="content-form--button" type="submit">管理者ログインする</button>
        </form>
    </div>
@endsection
