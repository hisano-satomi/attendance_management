<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class UserAuthController extends Controller
{
    public function loginPageShow()
    {
        return view('admin.auth.login'); // 管理者用ログイン画面
    }
}
