<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\LoginRequest;
use App\Models\User;

class AdminAuthController extends Controller
{
    // 管理者用ログイン画面表示
    public function loginPageShow()
    {
        return view('admin.login'); 
    }

    // 管理者用ログイン処理
    public function login(LoginRequest $request)
    {
        // バリデーション済み（LoginRequestで実行）
        $credentials = $request->only('email', 'password');

        // 認証処理（is_adminがtrueのユーザーのみ）
        if (auth()->attempt(array_merge($credentials, ['is_admin' => true]))) {
            $request->session()->regenerate();
            
            // 管理者としてログインしたことをセッションに保存
            $request->session()->put('was_admin', true);
            
            return redirect()->intended('/admin/attendances'); // ログイン成功後のリダイレクト先
        }

        // 認証失敗時の処理（登録済み情報のみ可）
        return back()
            ->withInput($request->only('email'))
            ->withErrors(['email' => 'ログイン情報が登録されていません']);
    }

    // 管理者用ログアウト処理
    public function logout()
    {
        auth()->logout();
        request()->session()->invalidate();
        request()->session()->regenerateToken();
        
        return redirect()->route('admin.login');
    }
}
