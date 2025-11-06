<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * ログイン機能のテスト
 * 
 * 実際のログイン処理をテストします。
 * POSTリクエストの送信、認証、セッションの検証などを学びます。
 */
class LoginTest extends TestCase
{
    use RefreshDatabase;

    /**
     * テスト1: 正しい認証情報でログインできる
     * 
     * 学ぶポイント:
     * - User::factory()->create() でテストユーザー作成
     * - $this->post() でPOSTリクエスト送信
     * - assertRedirect() でログイン後のリダイレクトを検証
     * - assertAuthenticated() で認証状態を検証
     */
    public function test_正しい認証情報でログインできる()
    {
        // テスト用ユーザーを作成
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => 'password123', // setPasswordAttributeで自動的にハッシュ化される
        ]);

        // ログインリクエストを送信
        $response = $this->post('/login', [
            'email' => 'test@example.com',
            'password' => 'password123',
        ]);

        // ログイン後、ホームページにリダイレクトされることを検証
        $response->assertRedirect('/attendance');

        // ユーザーが認証されていることを検証
        $this->assertAuthenticated();
    }

    /**
     * テスト2: 間違ったパスワードでログインできない
     * 
     * 学ぶポイント:
     * - assertSessionHasErrors() でエラーメッセージを検証
     * - assertGuest() で未認証状態を検証
     */
    public function test_間違ったパスワードでログインできない()
    {
        // テスト用ユーザーを作成
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => 'correct-password', // setPasswordAttributeで自動的にハッシュ化される
        ]);

        // 間違ったパスワードでログインを試みる
        $response = $this->post('/login', [
            'email' => 'test@example.com',
            'password' => 'wrong-password',
        ]);

        // ログインページにリダイレクトされる（または同じページに留まる）
        $response->assertStatus(302);

        // セッションにエラーがあることを検証
        $response->assertSessionHasErrors();

        // ユーザーは認証されていないことを検証
        $this->assertGuest();
    }

    /**
     * テスト3: 存在しないメールアドレスでログインできない
     */
    public function test_存在しないメールアドレスでログインできない()
    {
        // ログインを試みる（ユーザーは作成していない）
        $response = $this->post('/login', [
            'email' => 'notexist@example.com',
            'password' => 'password123',
        ]);

        $response->assertStatus(302);
        $response->assertSessionHasErrors();
        $this->assertGuest();
    }

    /**
     * テスト4: メールアドレスが空の場合、バリデーションエラーが発生する
     * 
     * 学ぶポイント:
     * - assertSessionHasErrors('field') で特定のフィールドのエラーを検証
     */
    public function test_メールアドレスが空だとバリデーションエラーになる()
    {
        $response = $this->post('/login', [
            'email' => '',
            'password' => 'password123',
        ]);

        // emailフィールドにエラーがあることを検証
        $response->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    /**
     * テスト5: パスワードが空の場合、バリデーションエラーが発生する
     */
    public function test_パスワードが空だとバリデーションエラーになる()
    {
        $response = $this->post('/login', [
            'email' => 'test@example.com',
            'password' => '',
        ]);

        $response->assertSessionHasErrors('password');
        $this->assertGuest();
    }

    /**
     * テスト6: ログイン後、認証が必要なページにアクセスできる
     * 
     * 学ぶポイント:
     * - actingAs() でユーザーとしてログインした状態を作る
     */
    public function test_ログイン後は認証が必要なページにアクセスできる()
    {
        $user = User::factory()->create();

        // actingAs() でユーザーとしてログインした状態にする
        $response = $this->actingAs($user)->get('/attendance');

        // ページが正常に表示される
        $response->assertStatus(200);
    }

    /**
     * テスト7: 未ログインでは認証が必要なページにアクセスできない
     */
    public function test_未ログインでは認証が必要なページにアクセスできない()
    {
        // ログインせずに /attendance にアクセス
        $response = $this->get('/attendance');

        // ログインページにリダイレクトされる
        $response->assertRedirect('/login');
    }

    /**
     * 【応用】テスト8: ログアウト機能のテスト
     * 
     * 学ぶポイント:
     * - ログアウト後は未認証状態になることを検証
     */
    public function test_ログアウトすると未認証状態になる()
    {
        $user = User::factory()->create();

        // ログイン状態にする
        $this->actingAs($user);

        // 認証されていることを確認
        $this->assertAuthenticated();

        // ログアウト
        $response = $this->post('/logout');

        // 未認証状態になることを検証
        $this->assertGuest();
        
        // ログインページにリダイレクトされる
        $response->assertRedirect('/login');
    }

    /**
     * 【応用】テスト9: 複数ユーザーのログインテスト
     */
    public function test_異なるユーザーでログインできる()
    {
        // ユーザーA
        $userA = User::factory()->create([
            'email' => 'usera@example.com',
            'password' => 'passwordA', // setPasswordAttributeで自動的にハッシュ化される
        ]);

        // ユーザーB
        $userB = User::factory()->create([
            'email' => 'userb@example.com',
            'password' => 'passwordB', // setPasswordAttributeで自動的にハッシュ化される
        ]);

        // ユーザーAでログイン
        $this->post('/login', [
            'email' => 'usera@example.com',
            'password' => 'passwordA',
        ]);
        $this->assertAuthenticated();

        // ログアウト
        $this->post('/logout');
        $this->assertGuest();

        // ユーザーBでログイン
        $this->post('/login', [
            'email' => 'userb@example.com',
            'password' => 'passwordB',
        ]);
        $this->assertAuthenticated();
    }
}

