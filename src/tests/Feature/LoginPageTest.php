<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * ログインページの表示テスト
 * 
 * Feature Test（機能テスト）では、HTTPリクエストを送信して
 * レスポンスが正しいかを検証します。
 */
class LoginPageTest extends TestCase
{
    use RefreshDatabase;

    /**
     * テスト1: 一般ユーザーログインページが表示される
     * 
     * 学ぶポイント:
     * - $this->get() でGETリクエストを送信
     * - assertStatus() でHTTPステータスコードを検証
     */
    public function test_一般ユーザーログインページが正常に表示される()
    {
        // /login にGETリクエストを送信
        $response = $this->get('/login');

        // HTTPステータスコード 200 (OK) が返されることを検証
        $response->assertStatus(200);
    }

    /**
     * テスト2: 管理者ログインページが表示される
     */
    public function test_管理者ログインページが正常に表示される()
    {
        $response = $this->get('/admin/login');

        $response->assertStatus(200);
    }

    /**
     * テスト3: ページに特定の文字列が含まれることを検証
     * 
     * 学ぶポイント:
     * - assertSee() でページ内の文字列を検証
     */
    public function test_ログインページにログインという文字が表示される()
    {
        $response = $this->get('/login');

        // ページ内に「ログイン」という文字列が含まれることを検証
        $response->assertSee('ログイン');
    }

    /**
     * テスト4: ページにフォーム要素が含まれることを検証
     * 
     * 学ぶポイント:
     * - assertSee() でHTML要素の存在を検証
     * - 第2引数にfalseを指定すると、HTMLエスケープせずに検証
     */
    public function test_ログインページにメールアドレス入力欄がある()
    {
        $response = $this->get('/login');

        // メールアドレスの入力欄が存在することを検証
        // name="email" の input タグが存在するか
        $response->assertSee('email', false);
    }

    /**
     * テスト5: ログインページにパスワード入力欄がある
     */
    public function test_ログインページにパスワード入力欄がある()
    {
        $response = $this->get('/login');

        $response->assertSee('password', false);
    }

    /**
     * テスト6: 存在しないページにアクセスすると404エラー
     * 
     * 学ぶポイント:
     * - assertStatus(404) で404エラーを検証
     */
    public function test_存在しないページは404エラーを返す()
    {
        $response = $this->get('/this-page-does-not-exist');

        $response->assertStatus(404);
    }

    /**
     * テスト7: ルートページはログインページにリダイレクトされる
     * 
     * 学ぶポイント:
     * - assertRedirect() でリダイレクトを検証
     * - assertStatus(302) でリダイレクトのステータスコードを検証
     */
    public function test_ルートページはログインページにリダイレクトされる()
    {
        $response = $this->get('/');

        // 302 (リダイレクト) が返されることを検証
        $response->assertStatus(302);
        
        // /login にリダイレクトされることを検証
        $response->assertRedirect('/login');
    }

    /**
     * 【応用】テスト8: 複数のアサーションを組み合わせる
     */
    public function test_ログインページが正しく機能している()
    {
        $response = $this->get('/login');

        // 複数の検証を一度に実行
        $response->assertStatus(200);           // ステータスコード 200
        $response->assertSee('ログイン');        // 「ログイン」の文字列
        $response->assertSee('email', false);   // email入力欄
        $response->assertSee('password', false); // password入力欄
    }
}

