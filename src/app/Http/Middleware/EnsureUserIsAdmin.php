<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class EnsureUserIsAdmin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        // ユーザーが認証されているかチェック
        if (!auth()->check()) {
            // 未認証の場合は管理者ログインページへリダイレクト
            return redirect('/admin/login');
        }

        // 認証済みユーザーが管理者かどうかをチェック
        if (!auth()->user()->is_admin) {
            // 管理者でない場合は403エラーまたは一般ユーザーページへリダイレクト
            abort(403, 'このページへのアクセス権限がありません。');
        }

        return $next($request);
    }
}
