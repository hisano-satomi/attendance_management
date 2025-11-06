<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;
use App\Models\Attendance;
use App\Models\BreakTime;
use Carbon\Carbon;

class UserAttendanceDetailTest extends TestCase
{
    use RefreshDatabase;

    protected function createUser()
    {
        return User::factory()->create([
            'name' => 'テストユーザー',
            'is_admin' => 0,
        ]);
    }

    /**
     * 勤怠詳細画面の「名前」がログインユーザーの氏名になっている
     */
    public function test_detail_page_shows_logged_in_user_name()
    {
        $user = $this->createUser();
        
        $attendance = Attendance::create([
            'user_id' => $user->id,
            'work_start' => Carbon::now(),
            'date' => Carbon::today(),
            'status' => 'working',
        ]);

        $response = $this->actingAs($user)->get('/attendance/detail/' . $attendance->id);

        $response->assertStatus(200);
        $response->assertSeeText('テストユーザー');
    }

    /**
     * 勤怠詳細画面の「日付」が選択した日付になっている
     */
    public function test_detail_page_shows_selected_date()
    {
        $user = $this->createUser();
        
        $date = Carbon::parse('2025-10-15');
        $attendance = Attendance::create([
            'user_id' => $user->id,
            'work_start' => $date,
            'date' => $date,
            'status' => 'working',
        ]);

        $response = $this->actingAs($user)->get('/attendance/detail/' . $attendance->id);

        $response->assertStatus(200);
        $response->assertSeeText('2025年');
        $response->assertSeeText('10月15日');
    }

    /**
     * 「出勤・退勤」にて記されている時間がログインユーザーの打刻と一致している
     */
    public function test_detail_page_shows_correct_clock_in_out_times()
    {
        $user = $this->createUser();
        
        $workStart = Carbon::parse('2025-10-15 09:00:00');
        $workStop = Carbon::parse('2025-10-15 18:00:00');
        
        $attendance = Attendance::create([
            'user_id' => $user->id,
            'work_start' => $workStart,
            'work_stop' => $workStop,
            'date' => $workStart->toDateString(),
            'status' => 'done',
        ]);

        $response = $this->actingAs($user)->get('/attendance/detail/' . $attendance->id);

        $response->assertStatus(200);
        // input要素の値を確認
        $response->assertSee('value="09:00"', false);
        $response->assertSee('value="18:00"', false);
    }

    /**
     * 「休憩」にて記されている時間がログインユーザーの打刻と一致している
     */
    public function test_detail_page_shows_correct_break_times()
    {
        $user = $this->createUser();
        
        $attendance = Attendance::create([
            'user_id' => $user->id,
            'work_start' => Carbon::parse('2025-10-15 09:00:00'),
            'date' => Carbon::parse('2025-10-15'),
            'status' => 'working',
        ]);

        $breakStart = Carbon::parse('2025-10-15 12:00:00');
        $breakStop = Carbon::parse('2025-10-15 13:00:00');
        
        BreakTime::create([
            'attendance_id' => $attendance->id,
            'break_start' => $breakStart,
            'break_stop' => $breakStop,
        ]);

        $response = $this->actingAs($user)->get('/attendance/detail/' . $attendance->id);

        $response->assertStatus(200);
        // input要素の値を確認
        $response->assertSee('value="12:00"', false);
        $response->assertSee('value="13:00"', false);
    }

    /**
     * 他のユーザーの勤怠詳細は閲覧できない
     */
    public function test_cannot_view_other_users_attendance_detail()
    {
        $user = $this->createUser();
        $otherUser = User::factory()->create([
            'name' => '他のユーザー',
            'is_admin' => 0,
        ]);
        
        $attendance = Attendance::create([
            'user_id' => $otherUser->id,
            'work_start' => Carbon::now(),
            'date' => Carbon::today(),
            'status' => 'working',
        ]);

        $response = $this->actingAs($user)->get('/attendance/detail/' . $attendance->id);

        // 403 Forbidden または 404 Not Found が期待される
        $this->assertTrue(in_array($response->status(), [403, 404]));
    }
}

