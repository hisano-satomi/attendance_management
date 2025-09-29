@extends('user.layout.before_header')

@section('content')
    <div class="content">
        <h2 class="content-title">会員登録</h2>
        <form class="content-form" action="{{ url('/register') }}" method="POST">
        @csrf
            <div class="content-form--item">
                <label for="name">名前</label>
                <input type="text" id="name" name="name">
            </div>
            <div class="content-form--item">
                <label for="email">メールアドレス</label>
                <input type="email" id="email" name="email">
            </div>
            <div class="content-form--item">
                <label for="password">パスワード</label>
                <input type="password" id="password" name="password">
            </div>
            <div class="content-form--item">
                <label for="password_confirmation">パスワード確認</label>
                <input type="password" id="password_confirmation" name="password_confirmation">
            </div>
            <button class="content-form--button" type="submit">登録する</button>
            <a class="login-link" href="{{ url('/login') }}">ログインはこちら</a>
        </form>
    </div>
@endsection
