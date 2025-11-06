<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;
use App\Models\Attendance;
use Carbon\Carbon;

class AttendanceDisplayTest extends TestCase
{
    use RefreshDatabase;

    protected function createUser()
    {
        return User::factory()->create([
            'is_admin' => 0,
        ]);
    }

    /**
     * 現在の日時情報がUIと同じ形式で出力されている
     */
    public function test_current_datetime_is_displayed_in_correct_format()
    {
        $user = $this->createUser();

        $response = $this->actingAs($user)->get('/attendance');

        $response->assertStatus(200);
        
        // 日付形式: YYYY/MM/DD(ddd)
        $expectedDate = Carbon::now()->isoFormat('YYYY/MM/DD(ddd)');
        $response->assertSee($expectedDate, false);
        
        // 時刻形式: HH:mm
        $response->assertSeeText(Carbon::now()->format('H:i'));
    }

    /**
     * 勤務外の場合、勤怠ステータスが正しく表示される
     */
    public function test_status_shows_not_working_when_no_attendance()
    {
        $user = $this->createUser();

        $response = $this->actingAs($user)->get('/attendance');

        $response->assertStatus(200);
        $response->assertSeeText('勤務外');
    }

    /**
     * 出勤中の場合、勤怠ステータスが正しく表示される
     */
    public function test_status_shows_working_when_clocked_in()
    {
        $user = $this->createUser();
        
        Attendance::create([
            'user_id' => $user->id,
            'work_start' => Carbon::now(),
            'date' => Carbon::today(),
            'status' => 'working',
        ]);

        $response = $this->actingAs($user)->get('/attendance');

        $response->assertStatus(200);
        $response->assertSeeText('出勤中');
    }

    /**
     * 休憩中の場合、勤怠ステータスが正しく表示される
     */
    public function test_status_shows_breaking_when_on_break()
    {
        $user = $this->createUser();
        
        Attendance::create([
            'user_id' => $user->id,
            'work_start' => Carbon::now()->subHours(2),
            'date' => Carbon::today(),
            'status' => 'breaking',
        ]);

        $response = $this->actingAs($user)->get('/attendance');

        $response->assertStatus(200);
        $response->assertSeeText('休憩中');
    }

    /**
     * 退勤済の場合、勤怠ステータスが正しく表示される
     */
    public function test_status_shows_done_when_clocked_out()
    {
        $user = $this->createUser();
        
        Attendance::create([
            'user_id' => $user->id,
            'work_start' => Carbon::now()->subHours(8),
            'work_stop' => Carbon::now(),
            'date' => Carbon::today(),
            'status' => 'done',
        ]);

        $response = $this->actingAs($user)->get('/attendance');

        $response->assertStatus(200);
        $response->assertSeeText('退勤済');
    }
}

