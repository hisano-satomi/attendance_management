<?php

namespace App\Providers;

use App\Actions\Fortify\CreateNewUser;
use App\Actions\Fortify\ResetUserPassword;
use App\Actions\Fortify\UpdateUserPassword;
use App\Actions\Fortify\UpdateUserProfileInformation;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;
use Laravel\Fortify\Fortify;
use Laravel\Fortify\Contracts\LogoutResponse;
use Laravel\Fortify\Contracts\LoginResponse;

class FortifyServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // ログイン後の処理をカスタマイズ（一般ユーザー用）
        $this->app->instance(LoginResponse::class, new class implements LoginResponse {
            public function toResponse($request)
            {
                // 一般ユーザーとしてログインしたことをセッションに保存
                $request->session()->put('was_admin', false);
                
                return redirect()->intended(config('fortify.home'));
            }
        });
        
        // ログアウト後のリダイレクト先をカスタマイズ
        $this->app->instance(LogoutResponse::class, new class implements LogoutResponse {
            public function toResponse($request)
            {
                // ログアウトしたユーザーが管理者かどうかを判定
                // セッションからユーザー情報を取得する前に保存
                $wasAdmin = $request->session()->get('was_admin', false);
                
                // 管理者の場合は管理者ログインページへ、一般ユーザーの場合は一般ユーザーログインページへ
                if ($wasAdmin) {
                    return redirect('/admin/login');
                }
                
                return redirect('/login');
            }
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // 一般ユーザー用ログイン画面
        Fortify::loginView(function () {
            return view('user.login');
        });

        // 一般ユーザー用登録画面
        Fortify::registerView(function () {
            return view('user.register');
        });

        Fortify::createUsersUsing(CreateNewUser::class);
        Fortify::updateUserProfileInformationUsing(UpdateUserProfileInformation::class);
        Fortify::updateUserPasswordsUsing(UpdateUserPassword::class);
        Fortify::resetUserPasswordsUsing(ResetUserPassword::class);

        RateLimiter::for('login', function (Request $request) {
            $throttleKey = Str::transliterate(Str::lower($request->input(Fortify::username())).'|'.$request->ip());

            return Limit::perMinute(5)->by($throttleKey);
        });

        RateLimiter::for('two-factor', function (Request $request) {
            return Limit::perMinute(5)->by($request->session()->get('login.id'));
        });
    }
}
