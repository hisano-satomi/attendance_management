<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;
use App\Models\Attendance;
use Carbon\Carbon;

class ClockInTest extends TestCase
{
    use RefreshDatabase;

    protected function createUser()
    {
        return User::factory()->create([
            'is_admin' => 0,
        ]);
    }

    /**
     * 出勤ボタンが正しく機能する
     */
    public function test_clock_in_button_works_correctly()
    {
        $user = $this->createUser();

        $response = $this->actingAs($user)->post('/work_start');

        $response->assertRedirect('/attendance');
        
        $this->assertDatabaseHas('attendances', [
            'user_id' => $user->id,
            'date' => Carbon::today(),
            'status' => 'working',
        ]);
    }

    /**
     * 出勤は一日一回のみできる
     */
    public function test_cannot_clock_in_twice_in_one_day()
    {
        $user = $this->createUser();

        // 1回目の出勤
        $this->actingAs($user)->post('/work_start');

        // 2回目の出勤（失敗するはず）
        $response = $this->actingAs($user)->post('/work_start');

        $response->assertRedirect('/attendance');
        
        // データベースに1件のみ存在することを確認
        $this->assertEquals(1, Attendance::where('user_id', $user->id)
            ->where('date', Carbon::today())
            ->count());
    }

    /**
     * 出勤時刻が勤怠一覧画面で確認できる
     */
    public function test_clock_in_time_is_visible_in_attendance_list()
    {
        $user = $this->createUser();
        
        $clockInTime = Carbon::now();
        Attendance::create([
            'user_id' => $user->id,
            'work_start' => $clockInTime,
            'date' => Carbon::today(),
            'status' => 'working',
        ]);

        $response = $this->actingAs($user)->get('/attendance/list');

        $response->assertStatus(200);
        // 勤怠一覧では時刻がテーブルに表示される
        $response->assertSee($clockInTime->format('H:i'), false);
    }

    /**
     * 未ログイン状態では出勤できない
     */
    public function test_guest_cannot_clock_in()
    {
        $response = $this->post('/work_start');

        $response->assertRedirect('/login');
    }
}

