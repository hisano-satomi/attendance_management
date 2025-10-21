<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>coachtech 勤怠管理アプリ</title>
    <link rel="stylesheet" href="{{ asset('css/sanitize.css') }}">
    <link rel="stylesheet" href="{{ asset('css/admin/common/after_header.css') }}">
    @yield('css')
</head>

<body>
    <header>
        <div class="header-container">
            <h1 class="header-logo">
                <a href="/"><img src="{{ asset('images/logo.svg') }}" alt="coachtech"></a>
            </h1>

            <ul class="header-nav">
                <li class="header-nav__item"><a href="{{ route('admin.attendance.list') }}">勤怠一覧</a></li>
                <li class="header-nav__item"><a href="{{ route('admin.users.list') }}">スタッフ一覧</a></li>
                <li class="header-nav__item"><a href="{{ route('admin.requests.list') }}">申請一覧</a></li>
                <li class="header-nav__item">
                    <form action="{{ route('admin.logout') }}" method="POST" class="logout-form">
                        @csrf
                        <button type="submit" class="logout-button">ログアウト</button>
                    </form>
                </li>
            </ul>
        </div>
    </header>

    <main>
        @yield('content')
    </main>
</body>
</html>