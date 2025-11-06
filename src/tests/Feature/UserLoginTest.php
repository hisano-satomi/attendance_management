<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;

class UserLoginTest extends TestCase
{
    use RefreshDatabase;

    /**
     * テスト用ユーザーを作成
     */
    protected function createUser()
    {
        return User::factory()->create([
            'email' => 'user@example.com',
            'password' => 'password123', // setPasswordAttributeで自動的にハッシュ化される
            'is_admin' => 0, // 一般ユーザー
        ]);
    }

    /**
     * メールアドレスが未入力の場合、バリデーションメッセージが表示される
     */
    public function test_email_is_required()
    {
        $response = $this->post('/login', [
            'email' => '',
            'password' => 'password123',
        ]);

        $response->assertSessionHasErrors('email');
    }

    /**
     * パスワードが未入力の場合、バリデーションメッセージが表示される
     */
    public function test_password_is_required()
    {
        $response = $this->post('/login', [
            'email' => 'user@example.com',
            'password' => '',
        ]);

        $response->assertSessionHasErrors('password');
    }

    /**
     * 登録内容と一致しない場合、バリデーションメッセージが表示される
     */
    public function test_login_fails_with_invalid_credentials()
    {
        $this->createUser();

        $response = $this->post('/login', [
            'email' => 'user@example.com',
            'password' => 'wrongpassword',
        ]);

        $response->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    /**
     * 存在しないメールアドレスでログインできない
     */
    public function test_login_fails_with_non_existent_email()
    {
        $response = $this->post('/login', [
            'email' => 'nonexistent@example.com',
            'password' => 'password123',
        ]);

        $response->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    /**
     * 正しい認証情報でログインできる
     */
    public function test_user_can_login_with_valid_credentials()
    {
        $user = $this->createUser();

        $response = $this->post('/login', [
            'email' => 'user@example.com',
            'password' => 'password123',
        ]);

        $response->assertRedirect('/attendance');
        $this->assertAuthenticatedAs($user);
    }

    /**
     * ログイン画面が表示される
     */
    public function test_login_page_can_be_displayed()
    {
        $response = $this->get('/login');

        $response->assertStatus(200);
        $response->assertViewIs('user.login');
    }
}

