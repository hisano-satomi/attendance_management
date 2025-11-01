<?php

namespace Tests\Unit;

use App\Models\User;
use App\Models\Attendance;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Userモデルのテスト
 * 
 * このテストではデータベースを使用します。
 * RefreshDatabaseトレイトを使うことで、テスト実行後に
 * データベースがクリーンアップされます。
 */
class UserModelTest extends TestCase
{
    // テスト実行時にデータベースをリフレッシュする
    use RefreshDatabase;

    /**
     * テスト1: Userモデルが作成できることを確認
     * 
     * 学ぶポイント:
     * - Factory を使ったモデル作成
     * - assertInstanceOf() でインスタンスの型を検証
     * - assertNotNull() で値が存在することを検証
     */
    public function test_ユーザーを作成できる()
    {
        // User::factory()->create() でテスト用ユーザーを作成
        $user = User::factory()->create([
            'name' => 'テスト太郎',
            'email' => 'test@example.com',
        ]);

        // Userのインスタンスであることを検証
        $this->assertInstanceOf(User::class, $user);
        
        // IDが割り振られていることを検証（DBに保存された証拠）
        $this->assertNotNull($user->id);
        
        // 名前とメールアドレスが正しく設定されているか検証
        $this->assertEquals('テスト太郎', $user->name);
        $this->assertEquals('test@example.com', $user->email);
    }

    /**
     * テスト2: データベースにユーザーが保存されることを確認
     * 
     * 学ぶポイント:
     * - assertDatabaseHas() でDBにデータが存在することを検証
     */
    public function test_ユーザーがデータベースに保存される()
    {
        $user = User::factory()->create([
            'name' => '山田太郎',
            'email' => 'yamada@example.com',
        ]);

        // データベースの users テーブルにデータが存在することを検証
        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'name' => '山田太郎',
            'email' => 'yamada@example.com',
        ]);
    }

    /**
     * テスト3: ユーザーと勤怠記録のリレーション（1対多）
     * 
     * 学ぶポイント:
     * - hasMany リレーションのテスト
     * - assertCount() でコレクションの数を検証
     * - assertInstanceOf() で型を検証
     */
    public function test_ユーザーは複数の勤怠記録を持つことができる()
    {
        // ユーザーを作成
        $user = User::factory()->create();

        // そのユーザーに紐づく勤怠記録を3つ作成
        $attendances = Attendance::factory()->count(3)->create([
            'user_id' => $user->id,
        ]);

        // ユーザーの attendances リレーションを取得
        $userAttendances = $user->attendances;

        // 勤怠記録が3つあることを検証
        $this->assertCount(3, $userAttendances);

        // 最初の勤怠記録が Attendance のインスタンスであることを検証
        $this->assertInstanceOf(Attendance::class, $userAttendances->first());

        // すべての勤怠記録が同じユーザーIDを持つことを検証
        foreach ($userAttendances as $attendance) {
            $this->assertEquals($user->id, $attendance->user_id);
        }
    }

    /**
     * テスト4: ユーザーに勤怠記録がない場合
     * 
     * 学ぶポイント:
     * - 空のコレクションの検証
     */
    public function test_勤怠記録がないユーザーは空のコレクションを返す()
    {
        // 勤怠記録を持たないユーザーを作成
        $user = User::factory()->create();

        // リレーションを取得
        $attendances = $user->attendances;

        // 空であることを検証
        $this->assertCount(0, $attendances);
        $this->assertTrue($attendances->isEmpty());
    }

    /**
     * テスト5: 複数ユーザーと勤怠記録の関係
     * 
     * 学ぶポイント:
     * - 複雑なリレーションのテスト
     * - 各ユーザーのデータが混ざっていないことを確認
     */
    public function test_複数ユーザーがそれぞれ独立した勤怠記録を持つ()
    {
        // ユーザーA: 勤怠記録2件
        $userA = User::factory()->create(['name' => 'ユーザーA']);
        Attendance::factory()->count(2)->create(['user_id' => $userA->id]);

        // ユーザーB: 勤怠記録3件
        $userB = User::factory()->create(['name' => 'ユーザーB']);
        Attendance::factory()->count(3)->create(['user_id' => $userB->id]);

        // ユーザーAの勤怠記録は2件
        $this->assertCount(2, $userA->attendances);

        // ユーザーBの勤怠記録は3件
        $this->assertCount(3, $userB->attendances);

        // ユーザーAの勤怠記録がすべてユーザーAのものであることを確認
        foreach ($userA->attendances as $attendance) {
            $this->assertEquals($userA->id, $attendance->user_id);
            $this->assertNotEquals($userB->id, $attendance->user_id);
        }
    }

    /**
     * テスト6: Factory を使わずに手動でデータを作成
     * 
     * 学ぶポイント:
     * - create() メソッドで直接作成
     * - より細かいコントロールが必要な場合に使用
     */
    public function test_手動でユーザーを作成できる()
    {
        $user = User::create([
            'name' => '手動作成ユーザー',
            'email' => 'manual@example.com',
            'password' => bcrypt('password'),
        ]);

        $this->assertDatabaseHas('users', [
            'name' => '手動作成ユーザー',
            'email' => 'manual@example.com',
        ]);
    }
}

