<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;

class RegisterTest extends TestCase
{
    use RefreshDatabase;

    /**
     * 名前が未入力の場合、バリデーションメッセージが表示される
     */
    public function test_name_is_required()
    {
        $response = $this->post('/register', [
            'name' => '',
            'email' => 'test@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertSessionHasErrors('name');
    }

    /**
     * メールアドレスが未入力の場合、バリデーションメッセージが表示される
     */
    public function test_email_is_required()
    {
        $response = $this->post('/register', [
            'name' => 'テストユーザー',
            'email' => '',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertSessionHasErrors('email');
    }

    /**
     * パスワードが8文字未満の場合、バリデーションメッセージが表示される
     */
    public function test_password_must_be_at_least_8_characters()
    {
        $response = $this->post('/register', [
            'name' => 'テストユーザー',
            'email' => 'test@example.com',
            'password' => 'pass123',
            'password_confirmation' => 'pass123',
        ]);

        $response->assertSessionHasErrors('password');
    }

    /**
     * パスワードが一致しない場合、バリデーションメッセージが表示される
     */
    public function test_password_confirmation_must_match()
    {
        $response = $this->post('/register', [
            'name' => 'テストユーザー',
            'email' => 'test@example.com',
            'password' => 'password123',
            'password_confirmation' => 'different123',
        ]);

        $response->assertSessionHasErrors('password');
    }

    /**
     * パスワードが未入力の場合、バリデーションメッセージが表示される
     */
    public function test_password_is_required()
    {
        $response = $this->post('/register', [
            'name' => 'テストユーザー',
            'email' => 'test@example.com',
            'password' => '',
            'password_confirmation' => '',
        ]);

        $response->assertSessionHasErrors('password');
    }

    /**
     * フォームに内容が入力されていた場合、データが正常に保存される
     */
    public function test_user_can_register_with_valid_data()
    {
        $response = $this->post('/register', [
            'name' => 'テストユーザー',
            'email' => 'test@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        // データベースにユーザーが保存されているか確認
        $this->assertDatabaseHas('users', [
            'name' => 'テストユーザー',
            'email' => 'test@example.com',
        ]);

        // 登録後、/attendanceにリダイレクトされるか確認
        $response->assertRedirect('/attendance');

        // ユーザーが認証されているか確認
        $this->assertAuthenticated();
    }

    /**
     * メールアドレスの形式が不正な場合、バリデーションメッセージが表示される
     */
    public function test_email_must_be_valid_format()
    {
        $response = $this->post('/register', [
            'name' => 'テストユーザー',
            'email' => 'invalid-email',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertSessionHasErrors('email');
    }

    /**
     * 既に登録されているメールアドレスは使用できない
     */
    public function test_email_must_be_unique()
    {
        // 既存ユーザーを作成
        User::factory()->create([
            'email' => 'existing@example.com',
        ]);

        $response = $this->post('/register', [
            'name' => 'テストユーザー',
            'email' => 'existing@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertSessionHasErrors('email');
    }
}

