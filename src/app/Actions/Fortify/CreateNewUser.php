<?php

namespace App\Actions\Fortify;

use App\Models\User;
use App\Http\Requests\User\RegisterRequest;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Laravel\Fortify\Contracts\CreatesNewUsers;

class CreateNewUser implements CreatesNewUsers
{
    use PasswordValidationRules;

    /**
     * Validate and create a newly registered user.
     *
     * @param  array<string, string>  $input
     */
    public function create(array $input): User
    {
        // RegisterRequestのインスタンスを作成してルールとメッセージを取得
        $registerRequest = new RegisterRequest();
        $rules = $registerRequest->rules();
        $messages = $registerRequest->messages();

        // バリデーションを実行
        Validator::make($input, $rules, $messages)->validate();

        return User::create([
            'name' => $input['name'],
            'email' => $input['email'],
            'password' => Hash::make($input['password']),
        ]);
    }
}
