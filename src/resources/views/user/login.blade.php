@extends('user.layout.before_header')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/user/login.css') }}">
@endsection

@section('content')
    <div class="content">
        <h2 class="content-title">ログイン</h2>
        <form class="content-form" action="{{ url('/login') }}" method="POST">
        @csrf
            <div class="content-form--item">
                <label for="email">メールアドレス</label>
                <input type="email" id="email" name="email">
                @error('email')
                    <p class="error-message">{{ $message }}</p>
                @enderror
            </div>
            <div class="content-form--item">
                <label for="password">パスワード</label>
                <input type="password" id="password" name="password">
                @error('password')
                    <p class="error-message">{{ $message }}</p>
                @enderror
            </div>
            <button class="content-form--button" type="submit">ログインする</button>
            <a class="register-link" href="{{ url('/register') }}">会員登録はこちら</a>
        </form>
    </div>
@endsection
