<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Http\Requests\User\LoginRequest;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class ValidateLoginRequest
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        // ログインルートの場合のみバリデーション
        if ($request->is('login') && $request->isMethod('post')) {
            // LoginRequestのルールとメッセージを取得
            $loginRequest = new LoginRequest();
            $rules = $loginRequest->rules();
            $messages = $loginRequest->messages();
            
            // バリデーション実行
            $validator = Validator::make($request->all(), $rules, $messages);
            
            if ($validator->fails()) {
                throw new ValidationException($validator);
            }
        }
        
        return $next($request);
    }
}

