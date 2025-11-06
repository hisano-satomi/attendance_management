<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;

class AdminLoginTest extends TestCase
{
    use RefreshDatabase;

    /**
     * テスト用管理者ユーザーを作成
     */
    protected function createAdmin()
    {
        return User::factory()->create([
            'email' => 'admin@example.com',
            'password' => 'adminpass123', // setPasswordAttributeで自動的にハッシュ化される
            'is_admin' => 1, // 管理者フラグ
        ]);
    }

    /**
     * メールアドレスが未入力の場合、バリデーションメッセージが表示される
     */
    public function test_email_is_required()
    {
        $response = $this->post('/admin/login', [
            'email' => '',
            'password' => 'adminpass123',
        ]);

        $response->assertSessionHasErrors('email');
    }

    /**
     * パスワードが未入力の場合、バリデーションメッセージが表示される
     */
    public function test_password_is_required()
    {
        $response = $this->post('/admin/login', [
            'email' => 'admin@example.com',
            'password' => '',
        ]);

        $response->assertSessionHasErrors('password');
    }

    /**
     * 登録内容と一致しない場合、バリデーションメッセージが表示される
     */
    public function test_login_fails_with_invalid_credentials()
    {
        $this->createAdmin();

        $response = $this->post('/admin/login', [
            'email' => 'admin@example.com',
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
        $response = $this->post('/admin/login', [
            'email' => 'nonexistent@example.com',
            'password' => 'adminpass123',
        ]);

        $response->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    /**
     * 正しい認証情報でログインできる
     */
    public function test_admin_can_login_with_valid_credentials()
    {
        $admin = $this->createAdmin();

        $response = $this->post('/admin/login', [
            'email' => 'admin@example.com',
            'password' => 'adminpass123',
        ]);

        // 管理者のリダイレクト先を確認（実際のルートに合わせて調整）
        $response->assertRedirect();
        $this->assertAuthenticatedAs($admin);
    }

    /**
     * 管理者ログイン画面が表示される
     */
    public function test_admin_login_page_can_be_displayed()
    {
        $response = $this->get('/admin/login');

        $response->assertStatus(200);
    }

    /**
     * 両方のフィールドが未入力の場合、バリデーションメッセージが表示される
     */
    public function test_both_fields_are_required()
    {
        $response = $this->post('/admin/login', [
            'email' => '',
            'password' => '',
        ]);

        $response->assertSessionHasErrors(['email', 'password']);
    }
}

