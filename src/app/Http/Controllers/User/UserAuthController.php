<?php

namespace App\Http\Controllers\user;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class UserAuthController extends Controller
{
    public function registerPageShow()
    {
        return view('user.auth.register'); // 一般ユーザー用登録画面
    }

    public function loginPageShow()
    {
        return view('user.auth.login'); // 一般ユーザー用ログイン画面
    }
}
