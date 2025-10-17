<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;

class AdminAuthController extends Controller
{
    // 管理者用ログイン画面表示
    public function loginPageShow()
    {
        return view('admin.login'); 
    }

    // 管理者用ログイン処理
    public function loginProcess(Request $request)
    {
        $credentials = $request->only('email', 'password');

        // 認証処理（is_adminがtrueのユーザーのみ）
        if (auth()->attempt(array_merge($credentials, ['is_admin' => true]))) {
            $request->session()->regenerate();
            
            // 管理者としてログインしたことをセッションに保存
            $request->session()->put('was_admin', true);
            
            return redirect()->intended('/admin/attendances'); // ログイン成功後のリダイレクト先
        }

        // 認証失敗時の処理
        return back()->onlyInput('email');
    }
}
