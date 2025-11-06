@extends('user.layout.before_header')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/user/email.css') }}">
@endsection

@section('content')
    <div class="email-verification-container">
        <div class="email-verification-content">
            <div class="email-verification-message">
                登録していただいたメールアドレスに認証メールを送付しました。<br>
                メール認証を完了してください。
            </div>

            @if(config('app.env') === 'local')
                <a href="http://localhost:8025/" target="_blank" class="email-verification-button">
                    認証はこちらから
                </a>
            @else
                <div class="email-verification-button-static">
                    認証はこちらから
                </div>
            @endif

            <div>
                <form method="POST" action="{{ route('verification.send') }}" class="email-resend-form">
                    @csrf
                    <button type="submit" class="email-resend-link">
                        認証メールを再送する
                    </button>
                </form>
            </div>
        </div>
    </div>
@endsection

