<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;
use App\Models\Attendance;
use App\Models\BreakTime;
use Carbon\Carbon;

class BreakTest extends TestCase
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
            'work_start' => Carbon::now()->subHours(2),
            'date' => Carbon::today(),
            'status' => 'working',
        ]);
    }

    /**
     * 休憩ボタンが正しく機能する
     */
    public function test_break_start_button_works_correctly()
    {
        $user = $this->createUser();
        $attendance = $this->createAttendance($user);

        $response = $this->actingAs($user)->post('/break_start');

        $response->assertRedirect('/attendance');
        
        $this->assertDatabaseHas('attendances', [
            'id' => $attendance->id,
            'status' => 'breaking',
        ]);
        
        $this->assertDatabaseHas('break_times', [
            'attendance_id' => $attendance->id,
        ]);
    }

    /**
     * 休憩は一日に何回でもできる
     */
    public function test_can_take_multiple_breaks_in_one_day()
    {
        $user = $this->createUser();
        $attendance = $this->createAttendance($user);

        // 1回目の休憩
        $this->actingAs($user)->post('/break_start');
        $this->actingAs($user)->post('/break_stop');

        // 2回目の休憩
        $this->actingAs($user)->post('/break_start');
        $this->actingAs($user)->post('/break_stop');

        // 3回目の休憩
        $this->actingAs($user)->post('/break_start');

        // 休憩記録が3件あることを確認
        $this->assertEquals(3, BreakTime::where('attendance_id', $attendance->id)->count());
    }

    /**
     * 休憩戻ボタンが正しく機能する
     */
    public function test_break_stop_button_works_correctly()
    {
        $user = $this->createUser();
        $attendance = $this->createAttendance($user);

        // 休憩開始
        $this->actingAs($user)->post('/break_start');

        // 休憩終了
        $response = $this->actingAs($user)->post('/break_stop');

        $response->assertRedirect('/attendance');
        
        $this->assertDatabaseHas('attendances', [
            'id' => $attendance->id,
            'status' => 'working',
        ]);
        
        // 休憩終了時刻が記録されていることを確認
        $breakTime = BreakTime::where('attendance_id', $attendance->id)->first();
        $this->assertNotNull($breakTime->break_stop);
    }

    /**
     * 休憩戻は一日に何回でもできる
     */
    public function test_can_end_multiple_breaks_in_one_day()
    {
        $user = $this->createUser();
        $attendance = $this->createAttendance($user);

        // 複数回の休憩と休憩戻
        for ($i = 0; $i < 3; $i++) {
            $this->actingAs($user)->post('/break_start');
            $this->actingAs($user)->post('/break_stop');
        }

        // 全ての休憩に終了時刻が記録されていることを確認
        $breakTimes = BreakTime::where('attendance_id', $attendance->id)->get();
        $this->assertEquals(3, $breakTimes->count());
        
        foreach ($breakTimes as $breakTime) {
            $this->assertNotNull($breakTime->break_stop);
        }
    }

    /**
     * 休憩時刻が勤怠一覧画面で確認できる
     */
    public function test_break_time_is_visible_in_attendance_list()
    {
        $user = $this->createUser();
        $attendance = $this->createAttendance($user);
        
        $breakStart = Carbon::now();
        $breakStop = Carbon::now()->addHour();
        
        BreakTime::create([
            'attendance_id' => $attendance->id,
            'break_start' => $breakStart,
            'break_stop' => $breakStop,
        ]);

        $response = $this->actingAs($user)->get('/attendance/list');

        $response->assertStatus(200);
        // 勤怠一覧では休憩時間の合計が表示される
        $breakDuration = $breakStop->diffInMinutes($breakStart);
        $breakHours = floor($breakDuration / 60);
        $breakMinutes = $breakDuration % 60;
        $response->assertSee(sprintf('%02d:%02d', $breakHours, $breakMinutes), false);
    }
}

