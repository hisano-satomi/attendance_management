<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;
use App\Models\Attendance;
use Carbon\Carbon;

class ClockOutTest extends TestCase
{
    use RefreshDatabase;

    protected function createUser()
    {
        return User::factory()->create([
            'is_admin' => 0,
        ]);
    }

    protected function createAttendance($user)
    {
        return Attendance::create([
            'user_id' => $user->id,
            'work_start' => Carbon::now()->subHours(8),
            'date' => Carbon::today(),
            'status' => 'working',
        ]);
    }

    /**
     * 退勤ボタンが正しく機能する
     */
    public function test_clock_out_button_works_correctly()
    {
        $user = $this->createUser();
        $attendance = $this->createAttendance($user);

        $response = $this->actingAs($user)->post('/work_stop');

        $response->assertRedirect('/attendance');
        
        $this->assertDatabaseHas('attendances', [
            'id' => $attendance->id,
            'status' => 'done',
        ]);
        
        // 退勤時刻が記録されていることを確認
        $attendance->refresh();
        $this->assertNotNull($attendance->work_stop);
    }

    /**
     * 退勤時刻が勤怠一覧画面で確認できる
     */
    public function test_clock_out_time_is_visible_in_attendance_list()
    {
        $user = $this->createUser();
        
        $clockOutTime = Carbon::now();
        Attendance::create([
            'user_id' => $user->id,
            'work_start' => Carbon::now()->subHours(8),
            'work_stop' => $clockOutTime,
            'date' => Carbon::today(),
            'status' => 'done',
        ]);

        $response = $this->actingAs($user)->get('/attendance/list');

        $response->assertStatus(200);
        // 勤怠一覧では時刻がテーブルに表示される
        $response->assertSee($clockOutTime->format('H:i'), false);
    }

    /**
     * 出勤していない状態では退勤できない
     */
    public function test_cannot_clock_out_without_clocking_in()
    {
        $user = $this->createUser();

        $response = $this->actingAs($user)->post('/work_stop');

        $response->assertRedirect('/attendance');
    }

    /**
     * 退勤後は再度退勤できない
     */
    public function test_cannot_clock_out_twice()
    {
        $user = $this->createUser();
        $attendance = $this->createAttendance($user);

        // 1回目の退勤
        $this->actingAs($user)->post('/work_stop');

        // 2回目の退勤（失敗するはず）
        $response = $this->actingAs($user)->post('/work_stop');

        $response->assertRedirect('/attendance');
    }
}

