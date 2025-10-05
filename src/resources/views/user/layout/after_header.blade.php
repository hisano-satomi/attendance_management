<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>coachtech 勤怠管理アプリ</title>
    <link rel="stylesheet" href="{{ asset('css/sanitize.css') }}">
    <link rel="stylesheet" href="{{ asset('css/user/common/after_header.css') }}">
    @yield('css')
</head>

<body>
    <header>
        <div class="header-container">
            <h1 class="header-logo">
                <a href="/"><img src="{{ asset('images/logo.svg') }}" alt="coachtech"></a>
            </h1>

            <ul class="header-nav">
                <li class="header-nav__item"><a href="">勤怠</a></li>
                <li class="header-nav__item"><a href="">勤怠一覧</a></li>
                <li class="header-nav__item"><a href="">申請</a></li>
                <li class="header-nav__item"><a href="">ログアウト</a></li>
            </ul>
        </div>
    </header>

    <main>
        @yield('content')
    </main>
</body>
</html>