<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;
use App\Models\Attendance;
use Carbon\Carbon;

class UserAttendanceListTest extends TestCase
{
    use RefreshDatabase;

    protected function createUser()
    {
        return User::factory()->create([
            'is_admin' => 0,
        ]);
    }

    /**
     * 自分が行った勤怠情報が全て表示されている
     */
    public function test_user_can_see_only_their_own_attendance()
    {
        $user = $this->createUser();
        $otherUser = $this->createUser();

        // 自分の勤怠記録
        Attendance::create([
            'user_id' => $user->id,
            'work_start' => Carbon::now(),
            'date' => Carbon::today(),
            'status' => 'working',
        ]);

        // 他人の勤怠記録
        Attendance::create([
            'user_id' => $otherUser->id,
            'work_start' => Carbon::now(),
            'date' => Carbon::today(),
            'status' => 'working',
        ]);

        $response = $this->actingAs($user)->get('/attendance/list');

        $response->assertStatus(200);
        // 自分の勤怠データのみが表示される（データベースで確認）
        $this->assertEquals(1, $user->attendances()->count());
        // 他人の勤怠データは表示されない
        $response->assertDontSeeText($otherUser->name);
    }

    /**
     * 勤怠一覧画面に遷移した際に現在の月が表示される
     */
    public function test_current_month_is_displayed_on_attendance_list()
    {
        $user = $this->createUser();

        $response = $this->actingAs($user)->get('/attendance/list');

        $response->assertStatus(200);
        $currentMonth = Carbon::now()->format('Y/m');
        $response->assertSeeText($currentMonth);
    }

    /**
     * 「前月」を押下した時に表示月の前月の情報が表示される
     */
    public function test_previous_month_button_shows_previous_month_data()
    {
        $user = $this->createUser();

        // 前月の勤怠記録を作成
        $lastMonth = Carbon::now()->subMonth();
        Attendance::create([
            'user_id' => $user->id,
            'work_start' => $lastMonth,
            'date' => $lastMonth,
            'status' => 'done',
        ]);

        $response = $this->actingAs($user)->get('/attendance/list?year=' . $lastMonth->year . '&month=' . $lastMonth->month);

        $response->assertStatus(200);
        $response->assertSeeText($lastMonth->format('Y/m'));
    }

    /**
     * 「翌月」を押下した時に表示月の翌月の情報が表示される
     */
    public function test_next_month_button_shows_next_month_data()
    {
        $user = $this->createUser();

        // 翌月の勤怠記録を作成
        $nextMonth = Carbon::now()->addMonth();
        Attendance::create([
            'user_id' => $user->id,
            'work_start' => $nextMonth,
            'date' => $nextMonth,
            'status' => 'done',
        ]);

        $response = $this->actingAs($user)->get('/attendance/list?year=' . $nextMonth->year . '&month=' . $nextMonth->month);

        $response->assertStatus(200);
        $response->assertSeeText($nextMonth->format('Y/m'));
    }

    /**
     * 「詳細」を押下すると、その日の勤怠詳細画面に遷移する
     */
    public function test_detail_button_redirects_to_attendance_detail()
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
    }
}

