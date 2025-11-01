@extends('user.layout.before_header')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/user/register.css') }}">
@endsection

@section('content')
    <div class="content">
        <h2 class="content-title">会員登録</h2>
        <form class="content-form" action="{{ url('/register') }}" method="POST">
        @csrf
            <div class="content-form--item">
                <label for="name">名前</label>
                <input type="text" id="name" name="name">
                @error('name')
                    <p class="error-message">{{ $message }}</p>
                @enderror
            </div>
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
                @if($errors->has('password') && !str_contains($errors->first('password'), '一致'))
                    <p class="error-message">{{ $errors->first('password') }}</p>
                @endif
            </div>
            <div class="content-form--item">
                <label for="password_confirmation">パスワード確認</label>
                <input type="password" id="password_confirmation" name="password_confirmation">
                @error('password_confirmation')
                    <p class="error-message">{{ $message }}</p>
                @enderror
                @if($errors->has('password') && str_contains($errors->first('password'), '一致'))
                    <p class="error-message">{{ $errors->first('password') }}</p>
                @endif
            </div>
            <button class="content-form--button" type="submit">登録する</button>
            <a class="login-link" href="{{ url('/login') }}">ログインはこちら</a>
        </form>
    </div>
@endsection
