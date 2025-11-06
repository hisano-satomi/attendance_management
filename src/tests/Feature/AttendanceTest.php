<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Attendance;
use App\Models\BreakTime;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Carbon\Carbon;
use Tests\TestCase;

/**
 * 勤怠記録機能のテスト
 * 
 * 実際の勤怠管理システムの機能をテストします。
 * データベース操作、ビジネスロジック、バリデーションなどを総合的にテストします。
 */
class AttendanceTest extends TestCase
{
    use RefreshDatabase;

    /**
     * テスト1: 出勤記録を作成できる
     * 
     * 学ぶポイント:
     * - 認証済みユーザーとしてリクエスト
     * - データベースに記録が保存されることを検証
     * - Carbon を使った日時の扱い
     */
    public function test_出勤記録を作成できる()
    {
        // テスト用ユーザーを作成
        $user = User::factory()->create();

        // ユーザーとしてログイン状態で出勤リクエストを送信
        $response = $this->actingAs($user)->post('/work_start');

        // リダイレクトされることを確認
        $response->assertRedirect('/attendance');

        // データベースに勤怠記録が保存されていることを検証
        $this->assertDatabaseHas('attendances', [
            'user_id' => $user->id,
            'date' => Carbon::today(),
            'status' => 'working',
        ]);

        // 実際にデータベースから取得して詳細を確認
        $attendance = Attendance::where('user_id', $user->id)->first();
        $this->assertNotNull($attendance);
        $this->assertNotNull($attendance->work_start);
        $this->assertEquals('working', $attendance->status);
    }

    /**
     * テスト2: 同じ日に複数回出勤登録できない
     * 
     * 学ぶポイント:
     * - ビジネスルールのテスト
     * - エラーメッセージの検証
     */
    public function test_同じ日に複数回出勤登録できない()
    {
        $user = User::factory()->create();

        // 1回目の出勤登録
        $this->actingAs($user)->post('/work_start');

        // 2回目の出勤登録を試みる
        $response = $this->actingAs($user)->post('/work_start');

        // データベースには1件のみ存在することを確認
        $count = Attendance::where('user_id', $user->id)
            ->where('date', Carbon::today())
            ->count();
        $this->assertEquals(1, $count);
    }

    /**
     * テスト3: 退勤記録を追加できる
     */
    public function test_退勤記録を追加できる()
    {
        $user = User::factory()->create();

        // まず出勤登録
        $this->actingAs($user)->post('/work_start');

        // 退勤登録
        $response = $this->actingAs($user)->post('/work_stop');

        $response->assertRedirect('/attendance');

        // データベースに退勤時刻が記録されていることを検証
        $attendance = Attendance::where('user_id', $user->id)->first();
        $this->assertNotNull($attendance->work_stop);
        $this->assertEquals('done', $attendance->status);
    }

    /**
     * テスト4: 出勤登録なしで退勤登録はできない
     */
    public function test_出勤登録なしで退勤登録できない()
    {
        $user = User::factory()->create();

        // 出勤登録せずに退勤登録を試みる
        $response = $this->actingAs($user)->post('/work_stop');

        // データベースに勤怠記録が存在しないことを確認
        $this->assertDatabaseMissing('attendances', [
            'user_id' => $user->id,
        ]);
    }

    /**
     * テスト5: 休憩開始を記録できる
     */
    public function test_休憩開始を記録できる()
    {
        $user = User::factory()->create();

        // 出勤登録
        $this->actingAs($user)->post('/work_start');

        // 休憩開始
        $response = $this->actingAs($user)->post('/break_start');

        $response->assertRedirect('/attendance');

        // ステータスが「休憩中」になっていることを確認
        $attendance = Attendance::where('user_id', $user->id)->first();
        $this->assertEquals('breaking', $attendance->status);

        // 休憩時間のレコードが作成されていることを確認
        $this->assertDatabaseHas('break_times', [
            'attendance_id' => $attendance->id,
        ]);
    }

    /**
     * テスト6: 休憩終了を記録できる
     */
    public function test_休憩終了を記録できる()
    {
        $user = User::factory()->create();

        // 出勤 → 休憩開始
        $this->actingAs($user)->post('/work_start');
        $this->actingAs($user)->post('/break_start');

        // 休憩終了
        $response = $this->actingAs($user)->post('/break_stop');

        $response->assertRedirect('/attendance');

        // ステータスが「勤務中」に戻っていることを確認
        $attendance = Attendance::where('user_id', $user->id)->first();
        $this->assertEquals('working', $attendance->status);

        // 休憩時間に終了時刻が記録されていることを確認
        $breakTime = BreakTime::where('attendance_id', $attendance->id)->first();
        $this->assertNotNull($breakTime);
        $this->assertNotNull($breakTime->break_stop);
    }

    /**
     * テスト7: 複数ユーザーの勤怠記録が混ざらない
     * 
     * 学ぶポイント:
     * - マルチユーザー環境のテスト
     * - データの独立性の検証
     */
    public function test_複数ユーザーの勤怠記録が独立している()
    {
        // ユーザーAとB
        $userA = User::factory()->create(['name' => 'ユーザーA']);
        $userB = User::factory()->create(['name' => 'ユーザーB']);

        // それぞれ出勤登録
        $this->actingAs($userA)->post('/work_start');
        $this->actingAs($userB)->post('/work_start');

        // ユーザーAの勤怠記録を取得
        $attendanceA = Attendance::where('user_id', $userA->id)->first();
        $this->assertEquals($userA->id, $attendanceA->user_id);

        // ユーザーBの勤怠記録を取得
        $attendanceB = Attendance::where('user_id', $userB->id)->first();
        $this->assertEquals($userB->id, $attendanceB->user_id);

        // レコードが2件存在することを確認
        $this->assertEquals(2, Attendance::count());
    }

    /**
     * テスト8: 勤怠一覧ページを表示できる
     */
    public function test_勤怠一覧ページを表示できる()
    {
        $user = User::factory()->create();

        // テスト用勤怠記録を3件作成
        Attendance::factory()->count(3)->create([
            'user_id' => $user->id,
        ]);

        // 勤怠一覧ページにアクセス
        $response = $this->actingAs($user)->get('/attendance/list');

        $response->assertStatus(200);
    }

    /**
     * テスト9: 勤怠詳細ページを表示できる
     */
    public function test_勤怠詳細ページを表示できる()
    {
        $user = User::factory()->create();

        // テスト用勤怠記録を作成
        $attendance = Attendance::factory()->create([
            'user_id' => $user->id,
        ]);

        // 勤怠詳細ページにアクセス
        $response = $this->actingAs($user)->get("/attendance/detail/{$attendance->id}");

        $response->assertStatus(200);
    }

    /**
     * 【応用】テスト10: 勤怠記録とリレーションのテスト
     * 
     * 学ぶポイント:
     * - リレーションを含む複雑なテスト
     * - Eager Loading の検証
     */
    public function test_勤怠記録は関連するユーザーと休憩時間を持つ()
    {
        $user = User::factory()->create();

        // 勤怠記録を作成
        $attendance = Attendance::factory()->create([
            'user_id' => $user->id,
        ]);

        // 休憩時間を2件作成
        BreakTime::factory()->count(2)->create([
            'attendance_id' => $attendance->id,
        ]);

        // リレーションを取得
        $attendanceWithRelations = Attendance::with(['user', 'breakTimes'])->find($attendance->id);

        // ユーザーのリレーションが存在する
        $this->assertNotNull($attendanceWithRelations->user);
        $this->assertEquals($user->id, $attendanceWithRelations->user->id);

        // 休憩時間が2件存在する
        $this->assertCount(2, $attendanceWithRelations->breakTimes);
    }
}

