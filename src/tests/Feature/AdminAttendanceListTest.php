<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;
use App\Models\Attendance;
use Carbon\Carbon;

class AdminAttendanceListTest extends TestCase
{
    use RefreshDatabase;

    protected function createAdmin()
    {
        return User::factory()->create([
            'is_admin' => 1,
        ]);
    }

    protected function createUser()
    {
        return User::factory()->create([
            'is_admin' => 0,
        ]);
    }

    /**
     * その日になされた全ユーザーの勤怠情報が正確に確認できる
     */
    public function test_admin_can_see_all_users_attendance()
    {
        $admin = $this->createAdmin();
        $user1 = $this->createUser();
        $user2 = $this->createUser();

        // 複数ユーザーの勤怠記録を作成
        Attendance::create([
            'user_id' => $user1->id,
            'work_start' => Carbon::now(),
            'date' => Carbon::today(),
            'status' => 'working',
        ]);

        Attendance::create([
            'user_id' => $user2->id,
            'work_start' => Carbon::now(),
            'date' => Carbon::today(),
            'status' => 'working',
        ]);

        $response = $this->actingAs($admin)->get('/admin/attendance/list');

        $response->assertStatus(200);
        $response->assertSeeText($user1->name);
        $response->assertSeeText($user2->name);
    }

    /**
     * 遷移した際に現在の日付が表示される
     */
    public function test_current_date_is_displayed_on_admin_attendance_list()
    {
        $admin = $this->createAdmin();

        $response = $this->actingAs($admin)->get('/admin/attendance/list');

        $response->assertStatus(200);
        $currentDate = Carbon::now()->format('Y/m/d');
        $response->assertSeeText($currentDate);
    }

    /**
     * 「前日」を押下した時に前の日の勤怠情報が表示される
     */
    public function test_previous_day_button_shows_previous_day_data()
    {
        $admin = $this->createAdmin();
        $user = $this->createUser();

        // 前日の勤怠記録を作成
        $yesterday = Carbon::yesterday();
        Attendance::create([
            'user_id' => $user->id,
            'work_start' => $yesterday,
            'date' => $yesterday,
            'status' => 'done',
        ]);

        $response = $this->actingAs($admin)->get('/admin/attendance/list?date=' . $yesterday->format('Y-m-d'));

        $response->assertStatus(200);
        $response->assertSeeText($yesterday->format('Y/m/d'));
    }

    /**
     * 「翌日」を押下した時に次の日の勤怠情報が表示される
     */
    public function test_next_day_button_shows_next_day_data()
    {
        $admin = $this->createAdmin();
        $user = $this->createUser();

        // 翌日の勤怠記録を作成
        $tomorrow = Carbon::tomorrow();
        Attendance::create([
            'user_id' => $user->id,
            'work_start' => $tomorrow,
            'date' => $tomorrow,
            'status' => 'done',
        ]);

        $response = $this->actingAs($admin)->get('/admin/attendance/list?date=' . $tomorrow->format('Y-m-d'));

        $response->assertStatus(200);
        $response->assertSeeText($tomorrow->format('Y/m/d'));
    }

    /**
     * 一般ユーザーは管理者用勤怠一覧にアクセスできない
     */
    public function test_regular_user_cannot_access_admin_attendance_list()
    {
        $user = $this->createUser();

        $response = $this->actingAs($user)->get('/admin/attendance/list');

        // 403 Forbidden が期待される
        $response->assertStatus(403);
    }
}

